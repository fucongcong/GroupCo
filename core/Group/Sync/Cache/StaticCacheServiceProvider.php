<?php

namespace Group\Sync\Cache;

use ServiceProvider;
use Group\Sync\Cache\StaticCacheService;

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
