<?php

namespace Group\Async\Tests;

use Test;
use AsyncFile;
use AsyncLog;

class AsyncFileTest extends Test
{
    public function unitwrite()
    {   
        $res = (yield AsyncFile::write(__ROOT__."runtime/test.txt", "hello wordls!"));

        $res = (yield AsyncFile::write(__ROOT__."runtime/test.txt", "hello wordls!", FILE_APPEND));
    }

    public function unitread()
    {   
        $content = (yield AsyncFile::read(__ROOT__."runtime/test.txt"));
    }

    public function unitlog()
    {   
        yield AsyncLog::info('hello world');

        yield AsyncLog::debug('test debug', ['foo' => 'bar']);

        yield AsyncLog::notice('hello world',[], 'group.com');

        yield AsyncLog::warning('hello world');

        yield AsyncLog::error('hello world');

        yield AsyncLog::critical('hello world');

        yield AsyncLog::alert('hello world');

        yield AsyncLog::emergency('hello world');
    }
}
