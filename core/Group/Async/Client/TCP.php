<?php

namespace Group\Async\Client;

use swoole_client;

class TCP extends Base
{
    protected $ip;

    protected $port;

    protected $data;

    protected $timeout = 5;

    protected $calltime;

    protected $client;

    protected $isInit = false;

    public function __construct($ip, $port)
    {
        $this->ip = $ip;
        $this->port = $port;

        $this->client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function call(callable $callback)
    {   
        if (!$this->isInit) {
            $this->client->on("connect", function ($cli) {
                $this->calltime = microtime(true);
                $cli->send($this->data);
            });

            $this->client->on('close', function ($cli) {
            });

            $this->client->on('error', function ($cli) use ($callback) {
                $this->calltime = microtime(true) - $this->calltime;
                call_user_func_array($callback, array('response' => false, 'error' => socket_strerror($cli->errCode), 'calltime' => $this->calltime));
            });

            $this->client->on("receive", function ($cli, $data) use ($callback) {
                $this->calltime = microtime(true) - $this->calltime;
                $cli->close();
                call_user_func_array($callback, array('response' => $data, 'error' => null, 'calltime' => $this->calltime));
            });
            $this->isInit = true;
        }

        $this->client->connect($this->ip, $this->port, $this->timeout, 1);
    }
}