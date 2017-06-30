<?php

namespace Group\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

class Log
{
    protected static $levels = [
        'debug'     => Logger::DEBUG,
        'info'      => Logger::INFO,
        'notice'    => Logger::NOTICE,
        'warning'   => Logger::WARNING,
        'error'     => Logger::ERROR,
        'critical'  => Logger::CRITICAL,
        'alert'     => Logger::ALERT,
        'emergency' => Logger::EMERGENCY,
    ];

    public static $cacheDir = "runtime/logs";

    public static function debug($message, array $context  = [], $model = 'web.app')
    {
        return self::writeLog(__FUNCTION__, $message, $context, $model);
    }

    public static function info($message, array $context  = [], $model = 'web.app')
    {
        return self::writeLog(__FUNCTION__, $message, $context, $model);
    }

    public static function notice($message, array $context  = [], $model = 'web.app')
    {
        return self::writeLog(__FUNCTION__, $message, $context, $model);
    }

    public static function warning($message, array $context  = [], $model = 'web.app')
    {
        return self::writeLog(__FUNCTION__, $message, $context, $model);
    }

    public static function error($message, array $context  = [], $model = 'web.app')
    {
        return self::writeLog(__FUNCTION__, $message, $context, $model);
    }

    public static function critical($message, array $context  = [], $model = 'web.app')
    {
        return self::writeLog(__FUNCTION__, $message, $context, $model);
    }

    public static function alert($message, array $context  = [], $model = 'web.app')
    {
        return self::writeLog(__FUNCTION__, $message, $context, $model);
    }

    public static function emergency($message, array $context  = [], $model = 'web.app')
    {
        return self::writeLog(__FUNCTION__, $message, $context, $model);
    }

    public static function clear()
    {

    }

    public static function writeLog($level, $message, $context, $model)
    {
        $logger = new Logger($model);
        $env = app('container')->getEnvironment();
        $path = app('container')->getAppPath();
        $cacheDir = static::$cacheDir;

        $logger->pushHandler(new StreamHandler($path.$cacheDir.'/'.$env.'.log', self::$levels[$level]));
        $logger->pushHandler(new FirePHPHandler());

        return $logger->$level($message, $context);

    }
}
