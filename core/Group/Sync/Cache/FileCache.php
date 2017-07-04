<?php

namespace Group\Sync\Cache;

use Group\Sync\Services\ServiceMap;

class FileCache extends ServiceMap
{
    public static function getMap()
    {
        return 'localFileCache';
    }
}
