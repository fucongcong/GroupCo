<?php

namespace Group\Test;

use PHPUnit_Framework_TestCase;
use Group\Container\Container;

abstract class Test extends PHPUnit_Framework_TestCase
{   
    protected $taskMethodPattern = '/^unit.+/i';

    public function __construct()
    {
        if (method_exists($this, '__initialize'))
            $this->__initialize();
    }

    public function testCo()
    {   
        $container = new Container();
        $task = new \Group\Coroutine\Task(1, $container, $this->scanTasks());
        $task->run();
    }

    protected function scanTasks()
    {
        $ref = new \ReflectionClass($this);
        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $methodName = $method->getName();
            if (!preg_match($this->taskMethodPattern, $methodName)) {
                continue;
            }

            yield $this->$methodName();
        }
    }

    public function releaseRedis()
    {
        app('redisPool')->close();
        $container = (yield getContainer());
        if (!is_null($container->singleton('redis'))) {
            $container->singleton('redis')->close();
            exit;
        }
    }

    public function releaseMysql()
    {
        app('mysqlPool')->close();
        $container = (yield getContainer());
        if (!is_null($container->singleton('mysql'))) {
            $container->singleton('mysql')->close();
        }
    }
}
