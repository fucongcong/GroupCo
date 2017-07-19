<?php

namespace Group\Services;

use AsyncService;
use Config;

class ServiceCenter
{   
    protected $services;

    protected $container;

    public function createService($serviceName)
    {   
        $ip = $this->services[$serviceName]['ip'];
        $port = $this->services[$serviceName]['port'];
        return $this->container->singleton(strtolower($serviceName), function() use ($serviceName, $ip, $port) {
            $service = new AsyncService($ip, $port);
            $service->setService($serviceName);
            return $service;
        });
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function setService($serviceName, $ip, $port)
    {
        $this->services[$serviceName] = ['ip' => $ip, 'port' => $port];
    }

    public function getService($serviceName)
    {
        if (isset($this->services[$serviceName])) {
            return $this->services[$serviceName];
        }

        return false;
    }
}


