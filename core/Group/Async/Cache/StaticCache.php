<?php

namespace Group\Async\Cache;

use Group\Async\Services\ServiceMap;

class StaticCache extends ServiceMap
{
    public static function getMap()
    {
        return 'staticCache';
    }
}
