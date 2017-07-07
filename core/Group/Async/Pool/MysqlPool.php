<?php

namespace Group\ASync\Pool;

use swoole_mysql;
use Group\ASync\Pool\Result;
use splQueue;
use Config;

class MysqlPool
{	
	//splQueue
	protected $poolQueue;

	//splQueue
	protected $taskQueue;

	//最大连接数
	protected $maxPool = 50;

	//配置
	protected $config;

	//连接池资源
	protected $resources = [];

	protected $ableCount = 0;

    protected $timeout = 5;

    protected $calltime;

	public function __construct()
	{
		$this->poolQueue = new splQueue();
		$this->taskQueue = new splQueue();
		$this->maxPool = Config::get('database::maxPool');
		$this->timeout = Config::get('database::timeout');

		$this->createResources();
	}

	//初始化连接数
	public function createResources()
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

        for ($i = $this->ableCount; $i < $this->maxPool; $i++) { 
        	$mysql = new swoole_mysql;
	        $mysql->connect($this->config, function(swoole_mysql $mysql, $res) {
	            if ($res) {
	                $this->put($mysql);
	            } else {
	            	$this->ableCount--;
	            }
	        });
	        $this->ableCount++;
        }
	}

	public function request($methd, $sql, callable $callback)
	{	
		//入队列
		$this->taskQueue->push(['methd' => $methd, 'sql' => $sql, 'callback' => $callback]);

		if (!$this->poolQueue->isEmpty()) {
			$this->doTask();
		}

		if (count($this->resources) < $this->maxPool && $this->ableCount < $this->maxPool) {
			$this->createResources();
		}
	}

	public function doTask()
	{
		$resource = false;
		while (!$this->poolQueue->isEmpty()) {
			$resource = $this->poolQueue->dequeue();
			if (!isset($this->resources[spl_object_hash($resource)])) {
				$resource = false;
				continue;
			}
		}

		if (!$resource) {
			return;
		}

		$task = $this->taskQueue->dequeue();
		$methd = $task['methd'];
		$callback = $task['callback'];
		$resource->$methd($task['sql'], function(swoole_mysql $mysql, $res) use ($callback) {
			if ($res === false) {
                call_user_func_array($callback, array('response' => false, 'error' => $mysql->error));
                $this->release($mysql);
                return;
            }
            $result = new Result($res, $mysql->affected_rows, $mysql->insert_id);
            call_user_func_array($callback, array('response' => $result));
            //释放资源
            $this->release($mysql);
		});
	}

	public function remove($resource)
	{
		unset($this->resources[spl_object_hash($resource)]);
	}

	/**
	 * put一个资源
	 */	
	public function put($resource)
	{
		$this->resources[spl_object_hash($resource)] = $resource;
		$this->poolQueue->enqueue($resource);

		if (!$this->taskQueue->isEmpty()) {
			$this->doTask();
		}
	}

	/**
	 * 释放资源入队列
	 */	
	public function release($resource)
	{
		$this->poolQueue->enqueue($resource);

		if (!$this->taskQueue->isEmpty()) {
			$this->doTask();
		}
	}

	/**
     * 关闭连接池
     */
    public function close()
    {
        foreach ($this->resources as $conn)
        {
            $conn->close();
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}
