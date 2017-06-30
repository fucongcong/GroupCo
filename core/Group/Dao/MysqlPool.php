<?php

namespace Group\Dao;

class MysqlPool
{	
	//splQueue
	protected $poolQueue;

	//splQueue
	protected $taskQueue;

	//最大连接数
	protected $maxPool;

	//配置
	protected $config;

	//连接池资源
	protected $resources = [];

	protected $ableCount = 0;

	protected $callback;

	public function __construct($config = [], $maxPool = 30)
	{
		$this->poolQueue = new splQueue();
		$this->taskQueue = new splQueue();
		$this->config = $config;
		$this->maxPool = $maxPool;
	}

	public function init(callable $callback)
	{
		$this->callback = $callback;
	}

	public function put($resource)
	{
		$this->resources[spl_object_hash($resource)] = $resource;
		$this->release($resource);
	}

	public function remove($resource)
	{
		unset($this->resources[spl_object_hash($resource)]);
	}

	/**
	 * 获取连接池资源
	 */
	public function getPool()
	{	
		if (!$this->taskQueue->isEmpty()) {
			$resource = $this->poolQueue->dequeue();
			$this->ableCount--;
			return $resource;
		}

		if (count($this->resources) < $this->maxPool && $this->ableCount < $this->maxPool) {
	
		}
	}

	/**
	 * 释放资源入队列
	 */	
	public function release($resource)
	{
		$this->poolQueue->enqueue($resource);
		$this->ableCount++;
	}


}