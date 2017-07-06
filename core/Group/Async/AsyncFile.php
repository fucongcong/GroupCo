<?php

namespace Group\Async;

use Config;
use Group\Async\Client\File;

class AsyncFile
{   
	/**
     * 异步读取 文件大小必须小于4M
     */
    public static function read($filename)
    {	
    	$file = new File();
        $file->read($filename);
        $res = (yield $file);

        yield $res['response'];
    }

    /**
     * 异步写入 文件大小必须小于4M
     */
    public static function write($filename, $content, $flags = 0)
    {	
    	self::checWritePermission($filename);

    	$file = new File();
        $file->write($filename, $content, $flags);
        $res = (yield $file);

        yield $res['response'];
    }

    private static function checWritePermission($filename)
    {	
    	$parts = explode('/', $filename);
        $file = array_pop($parts);
        $dir = '';
        foreach ($parts as $part) {
            if (!is_dir($dir .= "$part/")) {
                 mkdir($dir, 0755, true);
            }
        }

    	if (file_exists($filename) && !is_writable($filename)) {
    		throw new \Exception("The {$filename} not writable!");
		}
    }
}
