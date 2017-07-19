<?php

namespace Group\Async\Pool;

use Group\Async\Client\Base;

class MysqlProxy extends Base
{
    protected $calltime;

    protected $sql;

    protected $pool;

    protected $method;

    public function __construct($pool)
    {   
        $this->pool = $pool;
    }

    public function getConnection()
    {
        
    }

    public function query($sql)
    {
        $this->sql = $sql;
        $this->method = __FUNCTION__;
    }

    public function call(callable $callback)
    {   
        $this->pool->request($this->method, $this->sql, $callback);
    }
}
