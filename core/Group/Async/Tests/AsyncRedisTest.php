<?php

namespace Group\Async\Tests;

use Test;
use AsyncRedis;

class AsyncRedisTest extends Test
{
    public function unitredis()
    {   
        yield AsyncRedis::set('foo', 'bar');
        $res = (yield AsyncRedis::get('foo'));
        $this->assertEquals('bar', $res);
    }

    public function unitexpire()
    {
        yield AsyncRedis::expire('foo', 60);
    }

    public function unitdel()
    {
        yield AsyncRedis::del('foo');
    }

    public function unithGet()
    {   
        yield AsyncRedis::hSet('foo', 1, 'group');
        $res = (yield AsyncRedis::hGet('foo', 1));
        $this->assertEquals('group', $res);

        yield AsyncRedis::hDel('foo', 1);
    }
}
