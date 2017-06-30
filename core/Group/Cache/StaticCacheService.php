<?php

namespace Group\Cache;

class StaticCacheService
{
	protected static $store = [];

	/**
     * 设置一个静态缓存
     *
     * @param  string key
     * @param  string|array|object value
     * @param  boolean canUnset 能否释放
     */
	public function set($key, $value, $canUnset = true)
	{
		if ($canUnset) {
			self::$store[0][$key] = $value;
		} else {
			self::$store[1][$key] = $value;
		}	
	}

	/**
     * 获取一个静态缓存
     *
     * @param  string key
     * @param  default
     * @return  val
     */
	public function get($key, $default = null)
	{
		if (isset(self::$store[0][$key])) return self::$store[0][$key];
		if (isset(self::$store[1][$key])) return self::$store[1][$key];
		return $default;
	}

	/**
     * 刷新可释放的静态缓存
     *
     */
	public function flush()
	{
		self::$store[0] = [];
	}

	/**
     * 刷新所有静态缓存
     *
     */
	public function flushAll()
	{
		self::$store = [];
	}
}
