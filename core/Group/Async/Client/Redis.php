<?php

namespace Group\Async\Client;

use swoole_redis;

class Redis extends Base
{
    protected $ip;

    protected $port;

    protected $timeout = 5;

    protected $calltime;

    protected $method;

    protected $parameters;

    public function __construct($ip, $port, $method, $parameters, $timeout)
    {   
        $this->method = $method;
        $this->parameters = $parameters;
        parent::__construct($ip, $port, '', $timeout);
    }

    public function call(callable $callback)
    {
        $client = new swoole_redis;
        $client->connect($this->ip, $this->port, function (swoole_redis $client, $res) use ($callback) {
            $this->calltime = microtime(true);
            if ($res === false) {
                call_user_func_array($callback, array('response' => false, 'error' => "connect to redis server failed", 'calltime' => 0));
                return;
            }

            $method = $this->method;
            $parameters = $this->parameters;
            array_push($parameters, function(swoole_redis $client, $res) use ($callback) {
                $this->calltime = microtime(true) - $this->calltime;
                $client->close();
                if ($res === false) {
                    call_user_func_array($callback, array('response' => false, 'error' => $client->errMsg, 'calltime' => $this->calltime));
                } else {
                    call_user_func_array($callback, array('response' => $res, 'error' => null, 'calltime' => $this->calltime));
                }
            });

            call_user_func_array([$client, $method], $parameters);
        });
    }
}
