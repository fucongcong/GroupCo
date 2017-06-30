<?php

namespace Group\EventDispatcher;

use Group\Services\ServiceMap;

class EventDispatcher extends ServiceMap
{
    public static function getMap()
    {
        return 'eventDispatcher';
    }
}
