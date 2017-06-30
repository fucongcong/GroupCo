<?php
/*
beanstalkd -l 127.0.0.1 -p 11300 -b /
*/
namespace Group\Queue;

use Group\Queue\Bear;

class MessageQueue
{
    protected $argv;

    protected $loader;

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
\033[33m Usage: app/queue [start|restart|stop] \033[0m
";

    public function __construct($argv, $loader)
    {
        $this->argv = $argv;
        $this->loader = $loader;
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
        if (!in_array($argv[1], ['start', 'restart', 'stop'])) return;
        $bear = new Bear($this->loader);
        $function = $argv[1];
        $bear->$function();
        die;
    }
}
