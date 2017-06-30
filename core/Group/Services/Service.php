<?php

namespace Group\Services;

use Group\Services\ServiceClient;

class Service
{
    public function createService($serviceName)
    {
        return app()->singleton(strtolower($serviceName), function() use ($serviceName) {
            return new ServiceClient($serviceName);
        });
    }
}
