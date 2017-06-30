<?php

namespace Group\Services;

use Config;

class ServiceClient
{   
    protected $service;

    protected $serv;

    protected $port;

    protected $package_eof;

    protected $timeout = 1;

    protected $calls = [];

    protected $callId = 0;

    public function __construct($service)
    {
        $this->service = $service;
        $servers = Config::get("async::server");
        if (!isset($servers[$service])) throw new \Exception("Not Found the {$service}", 1);
        $this->serv = $servers[$service]['serv'];
        $this->port = $servers[$service]['port'];
        $this->package_eof = $servers[$service]['config']['package_eof'];
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function call($cmd, $data = [], $timeout = false)
    {   
        if (is_numeric($timeout)) {
            $this->timeout = $timeout;
        }

        $data = \Group\Async\DataPack::pack($cmd, $data);
        $data .= $this->package_eof;
        $res = (yield new \Group\Async\Client\TCP($this->serv, $this->port, $data, $this->timeout));
        if ($res && $res['response']) {
            $res['response'] = explode($this->package_eof, $res['response']);
            $res['response'] = json_decode($res['response'][0], true);

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
