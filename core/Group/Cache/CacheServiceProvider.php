<?php

namespace Group\Cache;

use ServiceProvider;
use Group\Cache\RedisCacheService;

class CacheServiceProvider extends ServiceProvider
{   
    protected $cache = null;
    /**
     * Register the service provider.
     *
     * @return object
     */
    public function register()
    {
        if (\Config::get("database::cache") == 'redis') $this->cache = 'redisCache';

        if ($this->cache == 'redisCache') {
            $this->app->singleton($this->cache, function () {
                return new RedisCacheService($this->app->singleton('redis'));
            });
        }
    }

    public function getName()
    {
        return $this->cache;
    }
}
