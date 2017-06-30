<?php

namespace Group\Async\Cache;

use Group\Async\Services\ServiceMap;

class FileCache extends ServiceMap
{
    public static function getMap()
    {
        return 'localFileCache';
    }
}
