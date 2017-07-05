<?php

namespace Group\Cache\Tests;

use Test;
use StaticCache;

class StaticCacheTest extends Test
{
    public function testset()
    {   
        StaticCache::set('foo', 'bar');
        StaticCache::set('ping', 'pong', false);
    }

    public function testget()
    {
        $this->assertEquals('bar', StaticCache::get('foo'));
    }

    public function testflush()
    {
        StaticCache::flush();
        $this->assertEquals(null, StaticCache::get('foo', null));
        $this->assertEquals('pong', StaticCache::get('ping'));
    }

    public function testflushall()
    {   
        StaticCache::flushAll();
        $this->assertEquals(null, StaticCache::get('ping', null));
    }
}
