<?php

namespace Group\Cache;

use Group\Services\ServiceMap;

class FileCache extends ServiceMap
{
    public static function getMap()
    {
        return 'localFileCache';
    }
}
