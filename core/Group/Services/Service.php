<?php

namespace Group\Services;

use AsyncService;
use Config;

class Service
{
    public function createService($serviceName)
    {
        return app()->singleton(strtolower($serviceName), function() use ($serviceName) {
        	$servers = Config::get("service::server");
            if (!isset($servers[$serviceName])) throw new \Exception("Not Found the {$serviceName}", 1);
            $serv = $servers[$serviceName]['serv'];
            $port = $servers[$serviceName]['port'];
            $package_eof = $servers[$serviceName]['config']['package_eof'];
            return new AsyncService($serv, $port, $package_eof);
        });
    }
}
