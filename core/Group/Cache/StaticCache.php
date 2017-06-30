<?php

namespace Group\Cache;

use Group\Services\ServiceMap;

class StaticCache extends ServiceMap
{
    public static function getMap()
    {
        return 'staticCache';
    }
}
