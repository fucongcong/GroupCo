<?php

namespace Group\Async\Client;

use swoole_redis;
use Config;

class Redis extends Base
{
    protected $ip;

    protected $port;

    protected $timeout = 5;

    protected $calltime;

    protected $method;

    protected $parameters;

    public function __construct($ip, $port, $timeout)
    {   
        parent::__construct($ip, $port, '', $timeout);
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    public function call(callable $callback)
    {   
        $config = Config::get("database::redis");
        if (isset($config['default']['auth'])) {
            $options['password'] = $config['default']['auth'];
        }
        $options['timeout'] = $this->timeout;

        $client = new swoole_redis($options);
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
