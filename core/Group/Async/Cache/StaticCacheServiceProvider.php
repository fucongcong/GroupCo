<?php

namespace Group\Async\Cache;

use ServiceProvider;
use Group\Async\Cache\StaticCacheService;

class StaticCacheServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return object
     */
    public function register()
    {
        $this->app->singleton('staticCache', function () {
            return new StaticCacheService();
        });
    }

    public function getName()
    {
        return 'staticCache';
    }
}
