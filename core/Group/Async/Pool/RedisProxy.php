<?php

namespace Group\Async\Pool;

use Group\Async\Client\Base;

class RedisProxy extends Base
{
    protected $parameters;

    protected $pool;

    protected $method;

    public function __construct($pool)
    {   
        $this->pool = $pool;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    public function call(callable $callback)
    {   
        $this->pool->request($this->method, $this->parameters, $callback);
    }
}
