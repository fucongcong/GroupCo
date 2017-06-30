<?php

namespace Group\Async;

use Group\Async\Server\Server;

class Async
{
    protected $argv;

    protected $help = "
\033[34m
 ----------------------------------------------------------

     -----        ----      ----      |     |   / ----
    /          | /        |      |    |     |   |      |
    |          |          |      |    |     |   | ----/
    |   ----   |          |      |    |     |   |
     -----|    |            ----       ----     |

 ----------------------------------------------------------
\033[0m
\033[31m 使用帮助: \033[0m
\033[33m Usage: app/async [需要执行的server名称] \033[0m
";

    public function __construct($argv)
    {
        $this->argv = $argv;
    }

    /**
     * run the console
     *
     */
    public function run()
    {
        $this->checkArgv();
        die($this->help);
    }

    /**
     * 检查输入的参数与命令
     *
     */
    protected function checkArgv()
    {
        $argv = $this->argv;
        if (!isset($argv[1])) return;

        $config = \Config::get("async::server");
        if (!isset($config[$argv[1]])) return;

        $log = isset($config[$argv[1]]['config']['log_file']) ? $config[$argv[1]]['config']['log_file'] : 'runtime/async/default.log';
        $log = explode("/", $log);
        \FileCache::set(array_pop($log), '', implode("/", $log)."/");
        
        $server = new Server($config[$argv[1]], $argv[1]);
        die;
    }
}
