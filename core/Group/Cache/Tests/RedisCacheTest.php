<?php

namespace Group\Cache\Tests;

use Test;
use Group\Cache\RedisCacheService;
use Redis;
use Group\Redis\RedisHelper;

class RedisCacheTest extends Test
{
    public function getRedis()
    {
        $redis = new Redis;

        $config = \Config::get("database::redis");

        $redis->pconnect($config['default']['host'], $config['default']['port']);

        if (isset($config['default']['auth'])){
            $redis->auth($config['default']['auth']);
        }

        $redis->setOption(Redis::OPT_PREFIX, isset($config['default']['prefix']) ? $config['default']['prefix'] : '');

        return new RedisCacheService($redis);
    }

    public function testSet()
    {
        $redis = $this->getRedis();
        $status = $redis->set('group', 'good', 3600);
        $this->assertTrue($status);

        $this->assertEquals('good', $redis->get('group'));
    }

    public function testHSet()
    {
        $redis = $this->getRedis();
        list($hashKey, $key) = RedisHelper::hashKey('group_', 'group');

        $status = $redis->hSet($hashKey, $key, 'foo');
        $this->assertEquals(1, $status);
        $this->assertEquals('foo', $redis->hGet($hashKey, $key));
    }

    public function testHDel()
    {
        $redis = $this->getRedis();
        list($hashKey, $key) = RedisHelper::hashKey('group_', 'group');

        $status = $redis->hDel($hashKey, $key);
        $this->assertEmpty($redis->hGet($hashKey, $key));
    }
}
