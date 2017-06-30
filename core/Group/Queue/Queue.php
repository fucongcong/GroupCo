<?php

namespace Group\Queue;

use Group\Services\ServiceMap;

class Queue extends ServiceMap
{
    public static function getMap()
    {
        return 'queue';
    }
}
