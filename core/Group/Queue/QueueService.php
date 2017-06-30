<?php

namespace Group\Queue;

use Pheanstalk\Pheanstalk;
use Group\Events\QueueEvent;

class QueueService
{
    protected $priority;

    protected $delaytime;

    protected $lifetime;

    protected $server;

    public function __construct()
    {   
        $this->initServer();

        $this->priority = \Config::get("queue::priority") ? : 10;
        $this->delaytime = \Config::get("queue::delaytime") ? : 0;
        $this->lifetime = \Config::get("queue::lifetime") ? : 60;
    }

    public function put($tube, $data, $priority = null, $delaytime = null, $lifetime = null)
    {   
        $priority ? : $this->priority;
        $delaytime ? : $this->delaytime;
        $lifetime ? : $this->lifetime;
        
        return $this->getPheanstalk()->useTube($tube)->put($data, $priority, $delaytime, $lifetime);
    }

    private function getPheanstalk()
    {
        if (count($this->server) <= 0) throw new \Exception("beanstalkd服务没有启动", 1);

        $key = array_rand($this->server);
        //抛出一个服务异常事件
        if(!$this->server[$key]->getConnection()->isServiceListening()) {

            \EventDispatcher::dispatch(QueueEvent::CRASH,
                new QueueEvent($this->server[$key]->getConnection()->getHost(),
                $this->server[$key]->getConnection()->getPort())
                );

            unset($this->server[$key]);
            return $this->getPheanstalk();
        }

        return $this->server[$key];
    }

    private function initServer()
    {
        $server = \Config::get("queue::server");
        if (isset($server['host']) && isset($server['port'])) {
            $this->init($server['host'], $server['port']);
        } else {
            foreach ($server as $one) {
                $this->init($one['host'], $one['port']);
            }
        }
    }

    private function init($host, $port)
    {
        $host ? : "127.0.0.1";
        $port ? : 11300;
        
        $this->server[] = new Pheanstalk($host, $port);
    }
}
