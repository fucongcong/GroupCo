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

    protected $options;

    protected $redis;

    protected $connected = false;

    public function __construct()
    {   
        $config = Config::get("database::redis");
        $this->ip = $config['default']['host'];
        $this->port = $config['default']['port'];
        if (isset($config['default']['auth'])) {
            $this->options['password'] = $config['default']['auth'];
        }
        $this->options['timeout'] = $this->timeout;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        $this->options['timeout'] = $this->timeout;
        $this->redis = new swoole_redis($this->options);
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
        $this->calltime = microtime(true);

        if ($this->connected === true) {
            $this->execute($callback);
        } else {
            $this->redis->connect($this->ip, $this->port, function (swoole_redis $client, $res) use ($callback) {
                if ($res === false) {
                    call_user_func_array($callback, array('response' => false, 'error' => "connect to redis server failed", 'calltime' => 0));
                    return;
                }

                $this->execute($callback);
                $this->connected = true;
            });
        }
    }

    public function execute($callback)
    {
        $method = $this->method;
        $parameters = $this->parameters;
        array_push($parameters, function(swoole_redis $client, $res) use ($callback) {
            $this->calltime = microtime(true) - $this->calltime;
            if ($res === false) {
                call_user_func_array($callback, array('response' => false, 'error' => $client->errMsg, 'calltime' => $this->calltime));
            } else {
                call_user_func_array($callback, array('response' => $res, 'error' => null, 'calltime' => $this->calltime));
            }
        });

        call_user_func_array([$this->redis, $method], $parameters);
    }

    public function close()
    {
        if ($this->connected === true) {
            $this->redis->close();
        }
    }
}
