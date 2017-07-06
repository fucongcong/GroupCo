<?php

namespace Group\Services;

use AsyncService;

class Service
{
    public function createService($serviceName)
    {
        return app()->singleton(strtolower($serviceName), function() use ($serviceName) {
            return new AsyncService($serviceName);
        });
    }
}
