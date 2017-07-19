<?php

namespace Group\Sync\Server;

use Group\Common\ArrayToolkit;
use Group\Exceptions\NotFoundException;
use Group\Common\ClassMap;
use swoole_table;
use swoole_process;
use swoole_server;
use Log;

class Server 
{
    protected $serv;

    protected $servName;

    protected $config;

    protected $taskRes;

    protected $taskCount;

    protected $insideTaskRes;

    protected $insideTaskCount;

    protected $pidPath;

    protected $argv;

    protected $setting = [
        //打开EOF检测
        'open_eof_check' => true, 
        //设置EOF 防止粘包
        'package_eof' => "\r\n", 
        'open_eof_split' => true, //底层拆分eof的包
        ];

    public function __construct($config =[], $servName, $argv = [])
    {   
        $this->argv = $argv;
        $config['config'] = array_merge($this->setting, $config['config']);
        $this->config = $config;
        $this->servName = $servName;
        $this->pidPath = __ROOT__."runtime/service/{$servName}/pid";
        $this->checkStatus();
        
        $this->serv = new swoole_server($config['serv'], $config['port']);
        $this->serv->set($config['config']);

        $this->serv->on('Start', [$this, 'onStart']);
        $this->serv->on('Shutdown', [$this, 'onShutdown']);
        $this->serv->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->serv->on('WorkerStop', [$this, 'onWorkerStop']);
        $this->serv->on('WorkerError', [$this, 'onWorkerError']);
        $this->serv->on('Receive', [$this, 'onReceive']);
        $this->serv->on('Task', [$this, 'onTask']);
        $this->serv->on('Finish', [$this, 'onFinish']);

        $this->serv->start();
    }

    public function onStart(swoole_server $serv)
    {
        if (PHP_OS !== 'Darwin') {
            swoole_set_process_name("php {$this->servName}: master");
        }
        echo $this->servName." Start...", PHP_EOL;

        $pid = $serv->master_pid;
        $this->mkDir($this->pidPath);
        file_put_contents($this->pidPath, $pid);

        $this->registerNode();
    }

    public function onShutdown(swoole_server $serv)
    {
        echo $this->servName." Shutdown...", PHP_EOL;

        $this->removeNode();
    }

    public function onWorkerStart(swoole_server $serv, $workerId)
    {
        if (function_exists('opcache_reset')) opcache_reset();
        $loader = require __ROOT__.'/vendor/autoload.php';
        $app = new \Group\Sync\SyncApp();
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
                if ($one == 'p') {
                    $this->sendData($serv, $fd, 1);
                    return;
                }

                list($cmd, $one) = \Group\Sync\DataPack::unpack($one);
                switch ($cmd) {
                    case 'close':
                        $this->sendData($serv, $fd, 1);
                        $serv->shutdown();
                        break;
                    case 'reload':
                        $this->sendData($serv, $fd, 1);
                        $serv->reload();
                        break;
                    default:
                        $serv->task(['cmd' => $cmd, 'data' => $one, 'fd' => $fd]);
                        break;
                }
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
                    $this->insideTaskRes[$forFd][$jobId] = [];
                    $this->insideTaskCount[$forFd][$jobId] = $data['data']['count'];
                } else {
                    $this->taskRes[$forFd] = [];
                    $this->taskCount[$forFd] = $data['data']['count'];
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
                if (isset($this->insideTaskRes[$forFd][$jobId])) {
                    $this->insideTaskRes[$forFd][$jobId][$callId] = $data['data'];
                    //内部的数据组合完毕的话 丢给上级
                    if ($this->insideTaskCount[$forFd][$jobId] == count($this->insideTaskRes[$forFd][$jobId])) {
                        //不存在父级的话 直接send
                        if (!isset($this->taskRes[$forFd])) {
                            $this->sendData($serv, $forFd, $this->insideTaskRes[$forFd][$jobId]);
                        } else {
                            //拼到父级里面去
                            $this->taskRes[$forFd][$jobId] = $this->insideTaskRes[$forFd][$jobId];
                            if ($this->taskCount[$forFd] == count($this->taskRes[$forFd])) {
                                $this->sendData($serv, $forFd, $this->taskRes[$forFd]);
                                unset($this->taskRes[$forFd]);
                                unset($this->taskCount[$forFd]);
                            }
                        }
                        unset($this->insideTaskRes[$forFd][$jobId]);
                        unset($this->insideTaskCount[$forFd][$jobId]);
                    }
                    return;
                }
            }

            if (isset($this->taskRes[$forFd])) {
                $this->taskRes[$forFd][$data['callId']] = $data['data'];
                if ($this->taskCount[$forFd] == count($this->taskRes[$forFd])) {
                    $this->sendData($serv, $forFd, $this->taskRes[$forFd]);
                    unset($this->taskRes[$forFd]);
                    unset($this->taskCount[$forFd]);
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

    private function sendData(swoole_server $serv, $fd, $data)
    {
        $fdinfo = $serv->connection_info($fd);
        if($fdinfo){
            //如果这个时候客户端还连接者的话说明需要返回返回的信息,
            //如果客户端已经关闭了的话说明不需要server返回数据
            //判断下data的类型
            if (is_array($data)){
                $data = json_encode($data);
            }
            $serv->send($fd, $data);
        }
    }

    private function doAction($cmd, array $parameters, $server)
    {   
        list($class, $action) = explode("::", $cmd);
        list($group, $class) = explode("\\", $class);
        $service = "src\\Service\\$group\\Service\\Impl\\{$class}ServiceImpl";
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

    private function checkStatus()
    {   
        if(isset($this->argv[2])) {

            if (!file_exists($this->pidPath)) {
                echo "pid不存在".PHP_EOL;
                exit;
            }

            switch ($this->argv[2]) {
                case 'reload':
                    $pid = file_get_contents($this->pidPath);
                    echo "当前进程".$pid.PHP_EOL;
                    echo "热重启中".PHP_EOL;
                    if ($pid) {
                        if (swoole_process::kill($pid, 0)) {
                            swoole_process::kill($pid, SIGUSR1);
                        }
                    }
                    echo "重启完成".PHP_EOL;
                    swoole_process::daemon(true);
                    $this->registerNode();
                    break;
                case 'stop':
                    $pid = file_get_contents($this->pidPath);
                    echo "当前进程".$pid.PHP_EOL;
                    echo "正在关闭".PHP_EOL;
                    if ($pid) {
                        if (swoole_process::kill($pid, 0)) {
                            swoole_process::kill($pid, SIGTERM);
                        }
                    }
                    echo "关闭完成".PHP_EOL;
                    @unlink($this->pidPath);
                    $this->removeNode();
                    break;
                default:
                    break;
            }
            exit;
        }
    }

    private function mkDir($dir)
    {
        $parts = explode('/', $dir);
        $file = array_pop($parts);
        $dir = '';
        foreach ($parts as $part) {
            if (!is_dir($dir .= "$part/")) {
                 mkdir($dir);
            }
        }
    }

    /**
     * 向服务治理中心注册当前节点
     */
    public function registerNode()
    {   
        if  (!isset($this->config['node_center'])) return;
        
        $map = new ClassMap();
        $services = array_unique($map->doSearch());

        if (isset($this->config['public'])) {
            $publics = explode(",", $this->config['public']);
            foreach ($publics as $key => $public) {
                if (!in_array($public, $services)) {
                    unset($publics[$key]);
                }
            }
            $services = $publics;
        }
        $services = implode(',', $services);

        $data = [
            'ip' => $this->config['ip'],
            'port' => $this->config['port'],
            'serverName' => $this->servName,
            'services' => $services,
        ];

        //若服务中心挂了，可以一直wait
        while (true) {
           $res = $this->post($this->config['node_center']."/node/add", $data);
            if ($res == 1) {
                break;
            }
            sleep(2);
        }
    }

    /**
     * 向服务治理中心移除当前节点
     */
    public function removeNode()
    {   
        if  (!isset($this->config['node_center'])) return;

        $data = [
            'ip' => $this->config['ip'],
            'port' => $this->config['port'],
        ];

        //若服务中心挂了，可以一直wait
        while (true) {
           $res = $this->post($this->config['node_center']."/node/remove", $data);
            if ($res == 1) {
                break;
            }
            sleep(2);
        }
    }

    public function post($url, $postData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); 
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }
}
