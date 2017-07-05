<?php

namespace Group\Cache;

use Config;
use \Group\Async\Client\Redis;

class AsyncRedis
{   
    protected static $host;

    protected static $port;

    protected static $timeout = 1;

    public static function setTimeout($timeout)
    {
        self::$timeout = $timeout;
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
        if (!self::$host) {
            $config = Config::get("database::redis");
            self::$host = $config['default']['host'];
            self::$port = $config['default']['port'];
        }

        $redis = new Redis(self::$host, self::$port, self::$timeout);
        $redis->setMethod($method);
        $redis->setParameters($parameters);

        $res = (yield $redis);
        
        if ($res && $res['response']) {
            yield $res['response'];
        }

        yield false;
    }
}
