<?php

namespace Group\Queue;

use Group\Queue\TubeListener;

class TubeTick
{   
    protected $pheanstalk;

    protected $workers;

    protected $mode;

    public function __construct($workers, $pheanstalk)
    {   
        $this->pheanstalk = $pheanstalk;
        $this->workers = $workers;
    }

    public function work()
    {   
        //是否有队列任务，有的话给worker进程发消息
        foreach ($this->workers as $pid => $worker) { 
            \Log::info("队列worker{$pid}启动", [], 'tube.work');    
            $data = $worker['tube'];
            if($data) $worker['process']->write($data);
        }
        
        // swoole_timer_tick(5000, function($timerId) {
            
        //     if(!$this->pheanstalk->getConnection()->isServiceListening()) {
        //         //现在是一旦队列服务器崩溃的话，处理队列的主进程将退出。当然可以设置成等待，知道队列服务器恢复，只要将下列代码注释
        //         \Log::emergency("队列服务器崩溃了!TubeTick监听器退出", [], 'tube.tick');
        //         swoole_timer_clear($timerId);
        //         return;
        //     }
        // });
    }

}