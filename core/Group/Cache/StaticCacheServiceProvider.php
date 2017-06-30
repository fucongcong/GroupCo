<?php

namespace Group\Cache;

use ServiceProvider;
use Group\Cache\StaticCacheService;

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
