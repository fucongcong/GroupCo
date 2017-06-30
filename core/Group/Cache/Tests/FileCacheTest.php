<?php

namespace Group\Cache\Tests;

use Test;
use FileCache;

class FileCacheTest extends Test
{
    public function testSet()
    {
        FileCache::set('test.php', ['testdfata' => 'datadata']);
        //指定路径
        FileCache::set('test.php', ['testdfata' => 'datadata'], 'runtime/cache/test/');

        $this->assertEquals(['testdfata' => 'datadata'], FileCache::get('test.php'));
        $this->assertEquals(['testdfata' => 'datadata'], FileCache::get('test.php', 'runtime/cache/test/'));
    }
}
