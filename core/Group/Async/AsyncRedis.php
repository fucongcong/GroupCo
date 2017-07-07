<?php

namespace Group\Async;

use Config;
use \Group\Async\Client\Redis;
use \Group\Async\Pool\RedisProxy;

class AsyncRedis
{   
    protected static $timeout = 1;

    protected static $usePool = true;

    public static function setTimeout($timeout)
    {
        self::$timeout = $timeout;
    }

    public static function enablePool($status)
    {
        self::$usePool = boolval($status);
    }

    /**
     * static call
     *
     * @param  method
     * @param  parameters
     * @return void
     */
    public static function __callStatic($method, $parameters)
    {   
        if (self::$usePool) {
            $pool = app('redisPool');
            $redis = new RedisProxy($pool);
        } else {
            $redis = new Redis();
            $redis->setTimeout(self::$timeout);
        }

        $redis->setMethod($method);
        $redis->setParameters($parameters);
        $res = (yield $redis);
        if ($res && $res['response']) {
            yield $res['response'];
        }

        yield false;
    }
}
