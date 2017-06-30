<?php

namespace Group\Async\Server;

use swoole_server;
use Group\Common\ArrayToolkit;
use swoole_table;
use Group\Exceptions\NotFoundException;
use Log;

class Server 
{
	protected $serv;

    protected $servName;

    protected $config;

    protected $task_res;

    protected $task_count;

    protected $inside_task_res;

    protected $inside_task_count;

	public function __construct($config =[], $servName)
	{  
        $this->serv = new swoole_server($config['serv'], $config['port']);
        $this->serv->set($config['config']);

        $this->serv->on('Start', [$this, 'onStart']);
        $this->serv->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->serv->on('WorkerStop', [$this, 'onWorkerStop']);
        $this->serv->on('WorkerError', [$this, 'onWorkerError']);
        $this->serv->on('Receive', [$this, 'onReceive']);
        $this->serv->on('Task', [$this, 'onTask']);
        $this->serv->on('Finish', [$this, 'onFinish']);

        $this->config = $config;
        $this->servName = $servName;
        
        $this->serv->start();
	}

    public function onStart(swoole_server $serv)
    {
        if (PHP_OS !== 'Darwin') {
            swoole_set_process_name("php {$this->servName}: master");
        }
        echo $this->servName." Start...", PHP_EOL;
    }

    public function onWorkerStart(swoole_server $serv, $workerId)
    {
        opcache_reset();
        $loader = require __ROOT__.'/vendor/autoload.php';
        $loader->setUseIncludePath(true);
        $app = new \Group\Async\AsyncApp();
        $app->initSelf();
        $app->registerServices();
        $app->singleton('container')->setAppPath(__ROOT__);

        //设置不同进程名字,方便grep管理
        if (PHP_OS !== 'Darwin') {
            if ($workerId >= $serv->setting['worker_num']) {
                swoole_set_process_name("php {$this->servName}: task");
            } else {
                swoole_set_process_name("php {$this->servName}: worker");
            }
        }
        // 判定是否为Task Worker进程
        // if ($workerId >= $serv->setting['worker_num']) {
        // } else {
        //     //$this->createTaskTable();
        // }
    }

    public function onWorkerStop(swoole_server $serv, $workerId)
    {
        if ($workerId >= $serv->setting['worker_num']) {
            echo 'Task #'. ($workerId - $serv->setting['worker_num']). ' Ended.'. PHP_EOL;
        } else {
            echo 'Worker #'. $workerId, ' Ended.'. PHP_EOL;
        }
    }

    public function onWorkerError(swoole_server $serv, $workerId, $workerPid, $exitCode)
    {
        echo "[", date('Y-m-d H:i:s'), "] Process Crash : Wid : $workerId error_code : $exitCode", PHP_EOL;
    }

    public function onReceive(swoole_server $serv, $fd, $fromId, $data)
    { 
        $data = trim($data);
        $data = explode($serv->setting['package_eof'], $data);
        $return = '';
        try {
            $config = $this->config;
            foreach($data as $one){
                list($cmd, $one) = \Group\Async\DataPack::unpack($one);
                $serv->task(['cmd' => $cmd, 'data' => $one, 'fd' => $fd]);
            }
        } catch (\Exception $e) {
            $this->record([
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
                'type'    => $e->getCode(),
            ]);
        }
    }

    public function onTask(swoole_server $serv, $fd, $fromId, $data)
    {
        try {
            $cmd = $data['cmd'];
            $cmdData = $data['data'];
            $server = [
                'serv' => $serv,
                'fd' => $data['fd'],
                'callId' => isset($data['callId']) ? $data['callId'] : $fd."-".$fromId,
                'fromId' => $fromId,
            ];

            if (is_array($cmd)) {
                $tasks = [];
                foreach ($cmd as $callId => $oneCmd) {
                    $tasks[$callId] = ['cmd' => $oneCmd, 'data' => $cmdData[$callId]];
                }
                return [
                    'fd' => $server['fd'],
                    'data' => [
                        'tasks' => $tasks,
                        'count' => count($tasks)
                    ]
                ];
            } else {
                return $this->doAction($cmd, $cmdData, $server);
            }
        } catch (\Exception $e) {
            $this->record([
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
                'type'    => $e->getCode(),
            ]);
        }
    }

    public function onFinish(swoole_server $serv, $fd, $data)
    {
        try {
            $forFd = $data['fd'];

            if (isset($data['data']['tasks'])) {
                //是不是内部的task任务
                if (isset($data['data']['jobId'])) {
                    $jobId = $data['data']['jobId'];
                    $this->inside_task_res[$forFd][$jobId] = [];
                    $this->inside_task_count[$forFd][$jobId] = $data['data']['count'];
                } else {
                    $this->task_res[$forFd] = [];
                    $this->task_count[$forFd] = $data['data']['count'];
                }

                foreach ($data['data']['tasks'] as $callId => $task) {
                    $serv->task(['cmd' => $task['cmd'], 'data' => $task['data'], 'fd' => $forFd, 'callId' => $callId]);
                }
                return;
            }

            $callId = $data['callId'];
            $callIds = explode("_", $callId);
            //是内部的task
            if (count($callIds) > 1) {
                $jobId = $callIds[0];
                $callId = $callIds[1];
                if (isset($this->inside_task_res[$forFd][$jobId])) {
                    $this->inside_task_res[$forFd][$jobId][$callId] = $data['data'];
                    //内部的数据组合完毕的话 丢给上级
                    if ($this->inside_task_count[$forFd][$jobId] == count($this->inside_task_res[$forFd][$jobId])) {
                        //不存在父级的话 直接send
                        if (!isset($this->task_res[$forFd])) {
                            $this->sendData($serv, $forFd, $this->inside_task_res[$forFd][$jobId]);
                        } else {
                            //拼到父级里面去
                            $this->task_res[$forFd][$jobId] = $this->inside_task_res[$forFd][$jobId];
                            if ($this->task_count[$forFd] == count($this->task_res[$forFd])) {
                                $this->sendData($serv, $forFd, $this->task_res[$forFd]);
                                unset($this->task_res[$forFd]);
                                unset($this->task_count[$forFd]);
                            }
                        }
                        unset($this->inside_task_res[$forFd][$jobId]);
                        unset($this->inside_task_count[$forFd][$jobId]);
                    }
                    return;
                }
            }

            if (isset($this->task_res[$forFd])) {
                $this->task_res[$forFd][$data['callId']] = $data['data'];
                if ($this->task_count[$forFd] == count($this->task_res[$forFd])) {
                    $this->sendData($serv, $forFd, $this->task_res[$forFd]);
                    unset($this->task_res[$forFd]);
                    unset($this->task_count[$forFd]);
                }
                return;
            }

            $this->sendData($serv, $forFd, $data['data']);

        } catch (\Exception $e) {
            $this->record([
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
                'type'    => $e->getCode(),
            ]);
        }
    }

    private function sendData(swoole_server $serv, $fd, $data){
        $fdinfo = $serv->connection_info($fd);
        if($fdinfo){
            //如果这个时候客户端还连接者的话说明需要返回返回的信息,
            //如果客户端已经关闭了的话说明不需要server返回数据
            //判断下data的类型
            if (is_array($data)){
                $data = json_encode($data);
            }
            $serv->send($fd, $data . $serv->setting['package_eof']);
        }
    }

    private function doAction($cmd, array $parameters, $server)
    {   
        list($class, $action) = explode("::", $cmd);
        list($group, $class) = explode("\\", $class);
        $service = "src\\Async\\$group\\Service\\Impl\\{$class}ServiceImpl";
        if (!class_exists($service)) {
            throw new NotFoundException("Service $service not found !");
        }

        $reflector = new \ReflectionClass($service);

        if (!$reflector->hasMethod($action)) {
            throw new NotFoundException("Service ".$service." exist ,But the Action ".$action." not found");
        }

        $instanc = $reflector->newInstanceArgs($server);
        $method = $reflector->getmethod($action);
        $args = [];
        foreach ($method->getParameters() as $arg) {
            $paramName = $arg ->getName();
            if (isset($parameters[$paramName])) $args[$paramName] = $parameters[$paramName];
        }

        return ['data' => $method->invokeArgs($instanc, $args), 'fd' => $server['fd'], 'callId' => $server['callId']];
    }

    private function record($e, $type = 'error')
    {   
        $levels = array(
            E_WARNING => 'Warning',
            E_NOTICE => 'Notice',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Runtime Notice',
            E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated',
            E_ERROR => 'Error',
            E_CORE_ERROR => 'Core Error',
            E_COMPILE_ERROR => 'Compile Error',
            E_PARSE => 'Parse',
        );
        if (!isset($levels[$e['type']])) {
            $level = 'Task Exception';
        } else {
            $level = $levels[$e['type']];
        }
        Log::$type('[' . $level . '] ' . $e['message'] . '[' . $e['file'] . ' : ' . $e['line'] . ']', []);
    }
}
