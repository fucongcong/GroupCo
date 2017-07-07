<?php

namespace Group\ASync\Pool;

use swoole_redis;
use splQueue;
use Config;
use Group\ASync\Pool\Pool;

class RedisPool extends Pool
{	
	//splQueue
	protected $poolQueue;

	//splQueue
	protected $taskQueue;

	//最大连接数
	protected $maxPool = 50;

	//配置
	protected $config;

	protected $options;

	//连接池资源
	protected $resources = [];

	protected $ableCount = 0;

    protected $timeout = 5;

	public function __construct()
	{
		$this->poolQueue = new splQueue();
		$this->taskQueue = new splQueue();

		$this->config = Config::get("database::redis");
		$this->maxPool = $this->config['maxPool'];
		$this->timeout = $this->config['timeout'];

		$this->createResources();
	}

	//初始化连接数
	public function createResources()
	{	
        $ip = $this->config['default']['host'];
        $port = $this->config['default']['port'];
        if (isset($this->config['default']['auth'])) {
            $this->options['password'] = $this->config['default']['auth'];
        }
        $this->options['timeout'] = $this->timeout;

        for ($i = $this->ableCount; $i < $this->maxPool; $i++) { 
        	$client = new swoole_redis($this->options);
	        $client->connect($ip, $port, function (swoole_redis $client, $res) {
	            if ($res) {
	                $this->put($client);
	            } else {
	            	$this->ableCount--;
	            }
	        });
	        $this->ableCount++;
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
		$method = $task['methd'];
        $parameters = $task['parameters'];
        $callback = $task['callback'];
        array_push($parameters, function(swoole_redis $client, $res) use ($callback) {
            if ($res === false) {
                call_user_func_array($callback, array('response' => false, 'error' => $client->errMsg));
            } else {
                call_user_func_array($callback, array('response' => $res));
            }
            $this->release($client);
        });

        call_user_func_array([$resource, $method], $parameters);
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
}
