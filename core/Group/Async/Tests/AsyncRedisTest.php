<?php

namespace Group\Async\Tests;

use Test;
use AsyncRedis;

class AsyncRedisTest extends Test
{
    public function unitredis()
    {   
        AsyncRedis::enablePool(false);

        yield AsyncRedis::set('foo', 'bar');
        $res = (yield AsyncRedis::get('foo'));
        $this->assertEquals('bar', $res);

        yield AsyncRedis::expire('foo', 60);

        yield AsyncRedis::del('foo');

        yield AsyncRedis::hSet('foo', 1, 'group');
        $res = (yield AsyncRedis::hGet('foo', 1));
        $this->assertEquals('group', $res);

        yield AsyncRedis::hDel('foo', 1);

        AsyncRedis::enablePool(true);

        yield AsyncRedis::set('foo', 'bar');
        $res = (yield AsyncRedis::get('foo'));
        $this->assertEquals('bar', $res);

        yield AsyncRedis::expire('foo', 60);

        yield AsyncRedis::del('foo');

        yield AsyncRedis::hSet('foo', 1, 'group');
        $res = (yield AsyncRedis::hGet('foo', 1));
        $this->assertEquals('group', $res);

        yield AsyncRedis::hDel('foo', 1);

        app('redisPool')->close();
        exit;
    }
}
