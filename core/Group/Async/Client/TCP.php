<?php

namespace Group\Async\Client;

class TCP extends Base
{
    protected $ip;

    protected $port;

    protected $data;

    protected $timeout = 5;

    protected $calltime;

    public function __construct($ip, $port, $data, $timeout)
    {
        parent::__construct($ip, $port, $data, $timeout);
    }

    public function call(callable $callback)
    {
        $client = new \swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

        $client->on("connect", function ($cli) {
            $cli->send($this->data);
        });

        $client->on('close', function ($cli) {
        });

        $client->on('error', function ($cli) use ($callback) {
            $this->calltime = microtime(true) - $this->calltime;
            call_user_func_array($callback, array('response' => false, 'error' => socket_strerror($cli->errCode), 'calltime' => $this->calltime));
        });

        $client->on("receive", function ($cli, $data) use ($callback) {
            $this->calltime = microtime(true) - $this->calltime;

            $cli->close();
            call_user_func_array($callback, array('response' => $data, 'error' => null, 'calltime' => $this->calltime));
        });

        if ($client->connect($this->ip, $this->port, $this->timeout, 1)) {
            $this->calltime = microtime(true);
        }
    }
}