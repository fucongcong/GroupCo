<?php

namespace Group\Async\Cache;

class RedisCacheService
{
    /**
     * redis对象
     *
     * @var  object
     */
    protected $redis;

    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    /**
     * 获取cache
     *
     * @param  cacheName
     * @return string|array|false
     */
    public function get($cacheName)
    {
        return $this->redis->get($cacheName);
    }

    /**
     * 设置cache
     *
     * @param  cacheName(string)
     * @param  data(array)
     * @param  expireTime(int)
     * @return boolean
     */
    public function set($cacheName, $data, $expireTime = 3600)
    {
        return $this->redis->set($cacheName, $data, $expireTime);
    }

    /**
     * 批量获取cache
     *
     * @param  cacheNames (array)
     * @return array
     */
    public function mGet(array $cacheNames)
    {
        return $this->redis->mGet($cacheNames);
    }

    /**
     * 获取hash cache
     *
     * @param  hashKey
     * @param  key
     * @return string|array|false
     */
    public function hGet($hashKey, $key)
    {
        return $this->redis->hGet($hashKey, $key);
    }

    /**
     * 设置hash cache
     *
     * @param  hashKey
     * @param  key
     * @param  data
     * @param  expireTime(int)
     * @return int
     */
    public function hSet($hashKey, $key, $data, $expireTime = 3600)
    {
        $status = $this->redis->hSet($hashKey, $key, $data);

        $this->redis->expire($hashKey, $expireTime);

        return $status;
    }

    /**
     * 删除hash
     *
     * @param  hashKey
     * @param  key
     * @return boolean
     */
    public function hDel($hashKey, $key = null)
    {
        if($key) return $this->redis->hDel($hashKey, $key);

        return $this->redis->hDel($hashKey);
    }

    /**
     * 返回一个redis对象
     *
     * @return object
     */
    public function getRedis()
    {
        return $this->redis;
    }

    public function __call($method, $parameters)
    {   
        try {
            return call_user_func_array([$this->redis, $method], $parameters);
        } catch (Exception $e) {
            throw new \Exception("Method [$method] does not exist.");
        }
    }
}
