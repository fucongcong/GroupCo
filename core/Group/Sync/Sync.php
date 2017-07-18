<?php

namespace Group\Sync;

use Group\Sync\Server\Server;

class Sync
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
\033[33m Usage: app/service [需要执行的server名称] \033[0m
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

        $config = \Config::get("service::server");
        $node_center = \Config::get("service::node_center");
        if ($node_center && $node_center != "") {
            $config[$argv[1]]['node_center'] = $node_center;
        }
        
        if (!isset($config[$argv[1]])) return;

        $log = isset($config[$argv[1]]['config']['log_file']) ? $config[$argv[1]]['config']['log_file'] : 'runtime/service/default.log';
        $log = explode("/", $log);
        \FileCache::set(array_pop($log), '', implode("/", $log)."/");
        
        $server = new Server($config[$argv[1]], $argv[1], $this->argv);
        die;
    }
}
