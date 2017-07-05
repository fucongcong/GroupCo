<?php

namespace Group\Async\Client;

use swoole_mysql;
use Config;

class Mysql extends Base
{
    protected $timeout = 5;

    protected $calltime;

    protected $config;

    protected $sql;

    public function __construct()
    {   
        $config = Config::get('database::pdo');
        $this->config = [
            'host' => $config['default']['host'],
            'port' => $config['default']['port'],
            'user' => $config['default']['user'],
            'password' => $config['default']['password'],
            'database' => $config['default']['dbname'],
            'charset' => $config['default']['charset'],
            'timeout' => $this->timeout,
        ];
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        $this->config['timeout'] = $timeout;
    }

    public function query($sql)
    {
        $this->sql = $sql;
    }

    public function call(callable $callback)
    {
        $mysql = new swoole_mysql;
        $mysql->connect($this->config, function(swoole_mysql $mysql, $res) use ($callback) {
            $this->calltime = microtime(true);
            if ($res === false) {
                call_user_func_array($callback, array('response' => false, 'error' => "connect to mysql server failed", 'calltime' => 0));
                return;
            }

            $mysql->query($this->sql, function(swoole_mysql $mysql, $res) use ($callback) {
                $this->calltime = microtime(true) - $this->calltime;
                if ($res === false) {
                    call_user_func_array($callback, array('response' => false, 'error' => $mysql->error, 'calltime' => $this->calltime));
                    return;
                }
                $result = new Result($res, $mysql->affected_rows, $mysql->insert_id);
                call_user_func_array($callback, array('response' => $result, 'error' => null, 'calltime' => $this->calltime));
                $mysql->close();
            });
        });
    }
}

/**
* result
*/
class Result
{
    protected $res;
    
    protected $affected_rows;

    protected $insert_id;

    public function __construct($res, $affected_rows, $insert_id)
    {
        $this->res = $res;
        $this->affected_rows = $affected_rows;
        $this->insert_id = $insert_id;
    }

    public function getResult()
    {
        return $this->res;
    }

    public function getAffectedRows()
    {
        return $this->affected_rows;
    }

    public function getInsertId()
    {
        return $this->insert_id;
    }
}
