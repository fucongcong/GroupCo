<?php

namespace Group\ASync\Pool;

use Group\ASync\Pool\RedisPool;

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