<?php

namespace Group\Async;

class AsyncService
{   
    protected $service = null;

    protected $serv;

    protected $port;

    protected $packageEof;

    protected $timeout = 1;

    protected $calls = [];

    protected $callId = 0;

    public function __construct($serv, $port, $packageEof = "\r\n")
    {   
        $this->serv = $serv;
        $this->port = $port;
        $this->packageEof = $packageEof;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function setService($service)
    {
        $this->service = $service;
    }

    public function call($cmd, $data = [], $timeout = false)
    {   
        if (!$this->serv || !$this->port) {
            yield false;
        }
        
        if (is_numeric($timeout)) {
            $this->timeout = $timeout;
        }

        if ($this->service) {
            $cmd = $this->service."\\".$cmd;
        }

        $data = \Group\Sync\DataPack::pack($cmd, $data);
        $data .= $this->packageEof;
        $res = (yield new \Group\Async\Client\TCP($this->serv, $this->port, $data, $this->timeout));

        if ($res && $res['response']) {
            $res['response'] = json_decode($res['response'], true);

            // if (app()->singleton('debugbar')->hasCollector('service')) {
            //     $array = [
            //         0 => $cmd,
            //         1 => $res['calltime'],
            //         2 => $res['response']
            //     ];
            //     app()->singleton('debugbar')->getCollector('service')->setData($array);
            // }

            yield $res['response'];
        }

        yield false;
    }

    public function addCall($cmd, $data = [])
    {   
        $callId = $this->callId;
        $this->calls['cmd'][$callId] = $cmd;
        $this->calls['data'][$callId] = $data;
        $this->callId++;

        return $callId;
    }

    public function multiCall($timeout = false)
    {   
        $res = (yield $this->call($this->calls['cmd'], $this->calls['data'], $timeout));
        $this->callId = 0;
        $this->calls = [];
        yield $res;
    }
}
