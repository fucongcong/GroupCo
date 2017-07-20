<?php

namespace Group\Async;

use Group\Async\Client\TCP;
use Group\Events\KernalEvent;
use Event;

class AsyncService
{   
    protected $service = null;

    protected $serv;

    protected $port;

    protected $packageEof;

    protected $timeout = 5;

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
        //$res = (yield new \Group\Async\Client\TCP($this->serv, $this->port, $data, $this->timeout));
        $container = (yield getContainer());
        $client = $container->singleton('tcp:'.$this->serv.':'.$this->port, function() {
            return new TCP($this->serv, $this->port);
        });

        $client->setTimeout($this->timeout);
        $client->setData($data);
        $res = (yield $client);

        if ($res && $res['response']) {
            //抛出一个事件出去，方便做上报
            yield $container->singleton('eventDispatcher')->dispatch(KernalEvent::SERVICE_CALL, 
                new Event(['cmd' => $cmd, 'calltime' => $res['calltime']]));

            $res['response'] = json_decode($res['response'], true);
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
