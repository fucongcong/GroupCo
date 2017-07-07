<?php

namespace Group\ASync\Pool;

use splQueue;

abstract class Pool
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

		$this->createResources();
	}

	//初始化连接数
	public function createResources() {}

	public function request($methd, $parameters, callable $callback)
	{	
		//入队列
		$this->taskQueue->push(['methd' => $methd, 'parameters' => $parameters, 'callback' => $callback]);

		if (!$this->poolQueue->isEmpty()) {
			$this->doTask();
		}

		if (count($this->resources) < $this->maxPool && $this->ableCount < $this->maxPool) {
			$this->createResources();
		}
	}

	public function doTask() {}

	public function remove($resource)
	{
		unset($this->resources[spl_object_hash($resource)]);
		$this->ableCount--;
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
    public function close() {}

    public function __destruct()
    {
        $this->close();
    }
}
