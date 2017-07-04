<?php

namespace Group\Sync\Cache;

use Group\Sync\Services\ServiceMap;

class StaticCache extends ServiceMap
{
    public static function getMap()
    {
        return 'staticCache';
    }
}
