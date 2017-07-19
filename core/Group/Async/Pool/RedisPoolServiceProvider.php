<?php

namespace Group\Async\Pool;

use Group\Async\Pool\RedisPool;

class RedisPoolServiceProvider extends \ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return object
     */
    public function register()
    {
        $this->app->singleton('redisPool', function () {
            return new RedisPool();
        });
    }

    public function getName()
    {
        return 'redisPool';
    }
}