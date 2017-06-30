<?php
//queue核心
namespace Group\Queue;

use swoole_process;
use Group\Queue\TubeTick;
use Pheanstalk\Pheanstalk;
use Group\Cache\BootstrapClass;

class Bear
{
    protected $logDir;

    protected $classCache;

    protected $workerNum;

    protected $workerPids;

    protected $workers;

    protected $tubes;

    protected $pheanstalk;

    protected $linstener;

    protected $server;

    protected $timer;

    public function __construct($loader)
    {      
        $this->initParam($loader); 
    }

    /**
     * 启动队列服务
     *
     */
    public function start()
    {	 
        $this->checkStatus();
        \Log::info("异步队列服务启动", [], 'queue.bear');
        //将主进程设置为守护进程
        swoole_process::daemon(true);
        //设置信号
        $this->setSignal();

        //启动N个work工作进程
        $this->startWorkers();

        //启动队列监听器
        $this->bindTubeTick();

        $this->setPid(); 
    }

    /**
     * 重启队列服务
     *
     */
    public function restart()
    {
        $this->stop();
        sleep(3);
        $this->start();
    }

    /**
     * 停止队列服务
     *
     */
    public function stop()
    {
      	$pid = $this->getPid();
        if (!empty($pid) && $pid) {
            if (swoole_process::kill($pid, 0)) {
                //杀掉worker进程
                foreach (\FileCache::get('work_ids', $this->logDir."/") as $work_id) {
                    swoole_process::kill($work_id, SIGTERM);
                }
            }   
        }
    }

    /**
     * 获取主进程pid
     *
     * @return int
     */
    public function getPid()
    {
    	if (file_exists($this->logDir."/pid"))
        return file_get_contents($this->logDir."/pid");
    }

    /**
     * 设置进程pid
     *
     */
    public function setPid()
    {
        $pid = posix_getpid();
        $parts = explode('/', $this->logDir."/pid");
        $file = array_pop($parts);
        $dir = '';
        foreach ($parts as $part) {
            if (!is_dir($dir .= "$part/")) {
                 mkdir($dir);
            }
        }
        file_put_contents("$dir/$file", $pid);
    }

    /**
     * 设置信号监听
     *
     */
    private function setSignal()
    {	
        //子进程结束时主进程收到的信号
        swoole_process::signal(SIGCHLD, function ($signo) {

            //kill掉所有worker进程 必须为false，非阻塞模式
            static $worker_count = 0;
            while($ret = swoole_process::wait(false)) {
                $worker_count++;
                \Log::info("PID={$ret['pid']}worker进程退出!", [], 'queue.bear');
                if ($worker_count >= $this->workerNum){
                    //删除pid文件
                    unlink($this->logDir."/work_ids");
                    unlink($this->logDir."/pid");
                    \Log::info("主进程退出!", [], 'queue.bear');
                    swoole_process::kill($this->getPid(), SIGKILL); 
                }
            }   
        });

    	//主进程重启时收到的信号,该信号用于用户自定义
    	// swoole_process::signal(SIGUSR1, function ($signo) {

    	// });
    }

    /**
     * 启动worker进程处理队列任务
     *
     */
    private function startWorkers()
    {   
        //启动worker进程
        for ($i = 0; $i < $this->workerNum; $i++) {
            $process = new swoole_process(array($this, 'workerCallBack'), true);
            $processPid = $process->start();
            $this->setWorkerPids($processPid);
            $this->workers[$processPid] = [
                'process' => $process,
                'tube' => $this->tubes[$i],
            ];
        }
    }

    /**
     * worker进程callback
     *
     * @param swoole_process worker
     */
    public function workerCallBack(swoole_process $worker) 
    {   
        $this->init();

        $server = $this->server;
        $listener = $this->listener;
        $timer = $this->timer;
        //worker进程
        swoole_event_add($worker->pipe, function($pipe) use ($worker, $server, $listener, $timer) {

            $recv = $worker->read();

            if (isset($server['host']) && isset($server['port'])) {
                $this->handleJob($server, $recv, $listener, $timer);
            } else {
                foreach ($server as $one) {
                    $this->handleJob($one, $recv, $listener, $timer);
                }
            }      
        });

        //接受退出的信号
        swoole_process::signal(SIGTERM, function ($signo) use ($worker) {
            $worker->exit();
        });
    }

    private function handleJob($server, $recv, $listener, $timer)
    {
        $pheanstalk = new Pheanstalk($server['host'], $server['port'], 10);
        
        swoole_timer_tick(intval($timer), function($timerId) use ($recv, $listener, $pheanstalk){
        
            $recv = $listener->getJob($recv, $pheanstalk);
            $recv = unserialize($recv); 
            if (is_object($recv['job'])) {
                try{
                    foreach ($recv['handle'] as $handerClass => $job) {
                       $handler = new $handerClass($recv['job']->getId(), $recv['job']->getData());
                       $handler->handle();
                    }
                    //删除任务 是否应该放到用户队列任务 让用户自行删除？包括可以操作release和bury
                    $pheanstalk->delete($recv['job']);
                    //\Log::info("jobId:".$recv['job']->getId()."任务完成".$recv['job']->getData(), [], 'queue.worker');
                }catch(\Exception $e){
                    \Log::error("jobId:".$recv['job']->getId()."任务出错了！", ['jobId' => $recv['job']->getId(), 'jobData' => $recv['job']->getData(), 'message' => $e->getMessage()], 'queue.worker');
                }
            } 
        });
    }

    /**
     * 设置worker进程的pid
     *
     * @param pid int
     */
    private function setWorkerPids($pid)
    {
        $this->workerPids[] = $pid;
        \FileCache::set('work_ids', $this->workerPids, $this->logDir."/");
    }

    /**
     * 绑定队列事件
     *
     */
    private function bindTubeTick()
    {
        $tick = new TubeTick($this->workers, $this->pheanstalk);
        $tick->work();
    }

    /**
     * 初始化参数
     *
     * @param loader 
     */
    private function initParam($loader)
    {
    	$this->logDir = \Config::get("queue::log_dir"); 
        \Log::$cacheDir = $this->logDir;
    	
        $this->classCache = \Config::get("queue::class_cache"); 
        $server = \Config::get("queue::server");
        
        $this->server = $server;

        if (isset($server['host']) && isset($server['port'])) {
            $this->initPheanstalk($server['host'], $server['port']);
        } else {
            foreach ($server as $one) {
                $this->initPheanstalk($one['host'], $one['port']);
            }
        }

        //开始队列任务的监听
        $this->listener = new TubeListener();
        $this->workerNum = $this->setWorkNum($this->listener->getJobs());
        $this->tubes = $this->listener->getTubes();
        $this->timer = \Config::get("queue::timer"); 
        $this->bootstrapClass($loader, $this->listener->getJobs());  
    }

    private function initPheanstalk($host, $port)
    {
        $pheanstalk = new Pheanstalk($host, $port, 10, true);
        if(!$pheanstalk->getConnection()->isServiceListening()) {
            echo("beanstalkd队列服务器连接失败,地址:{$host}:{$port}");
        } else {
            $this->pheanstalk[] = $pheanstalk;
        }
    }

    /**
     * 缓存类文件
     *
     * @param loader 
     * @param jobs 
     */
    private function bootstrapClass($loader, $jobs)
    {
        $classCache = new BootstrapClass($loader, $this->classCache);
        foreach ($jobs as $job) {
            foreach ($job as $handerClass => $value) {
                $classCache->setClass($handerClass);
            }  
        }
        $classCache->bootstrap();
        require $this->classCache;
    }

    /**
     * 设置worker数量
     *
     * @param jobs 
     */
    private function setWorkNum($jobs)
    {
        $workerNum = 0;
        foreach ($jobs as $job) {
            $taskWorkerNum = 0;
            foreach ($job as $key => $value) {
               if ($taskWorkerNum < $value['task_worker_num']) {
                    $taskWorkerNum = $value['task_worker_num'];
                }
            }
            $workerNum += $taskWorkerNum;
            
        }

        return $workerNum;
    }

    private function checkStatus()
    {
        if ($this->getPid()) {
            if (swoole_process::kill($this->getPid(), 0)) {
                exit('队列服务已启动！');
            }
        }
    }

    private function init()
    {   
        if(function_exists("opcache_reset")) opcache_reset();
        
        $loader = require __ROOT__.'/vendor/autoload.php';
        $loader->setUseIncludePath(true);
        $app = new \Group\App\App();
        $app->initSelf();
        $app->doBootstrap($loader);
        $app->registerServices();
        $app->singleton('container')->setAppPath(__ROOT__);
    }
}
