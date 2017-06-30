<?php

namespace Group\Async\Cache;

use Group\Async\Services\ServiceMap;

class Cache extends ServiceMap
{
    public static function getMap()
    {
        if (\Config::get("database::cache") != 'redis') return '';

        return 'redisCache';
    }

    /**
     * 返回一个rediscache的对象
     *
     * @return object
     */
    public static function redis()
    {
        return app()->singleton('redisCache');
    }
}
