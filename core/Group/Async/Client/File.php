<?php

namespace Group\Async\Client;

use Config;

class File extends Base
{
    protected $filename;

    protected $content;

    protected $action;

    protected $flags = 0;

    public function __construct() {}

    public function read($filename)
    {
        $this->filename = $filename;
        $this->action = __FUNCTION__;
    }

    public function write($filename, $content, $flags)
    {
        $this->filename = $filename;
        $this->content = $content;
        $this->flags = $flags;
        $this->action = __FUNCTION__;
    }

    public function call(callable $callback)
    {
        switch ($this->action) {
            case 'read':
                swoole_async_readfile($this->filename, function($filename, $content) use ($callback) {
                    call_user_func_array($callback, array('response' => $content, 'error' => null, 'calltime' => 0));
                });
                break;
            case 'write':
                swoole_async_writefile($this->filename, $this->content, function($filename) use ($callback) {
                    call_user_func_array($callback, array('response' => true, 'error' => null, 'calltime' => 0));
                }, $this->flags);
                break;
            default:
                break;
        }
    }
}
