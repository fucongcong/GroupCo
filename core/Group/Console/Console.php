<?php

namespace Group\Console;

class Console
{
    protected $argv;

    /**
     * 命令的定义集合
     *
     */
    protected $options = [
        'generate:service' => [
            'args' => 'name',
            'command' => 'Group\Console\Command\GenerateServiceCommand',
            'help' => '生成一个自定义service'
        ],
        'generate:controller' => [
            'args' => 'name|groupname:name',
            'command' => 'Group\Console\Command\GenerateControllerCommand',
            'help' => '生成一个自定义controller(默认存放在src/Web,如果想要指定分组 groupname:name)'
        ],
        'sql:generate' => [
            'command' => 'Group\Console\Command\SqlGenerateCommand',
            'help' => '生成一个sql执行模板(存放于app/sql)'
        ],
        'sql:migrate' => [
            'command' => 'Group\Console\Command\SqlMigrateCommand',
            'help' => '执行sql更新'
        ],
        'sql:rollback' => [
            'args' => 'version',
            'command' => 'Group\Console\Command\SqlRollBackCommand',
            'help' => '执行sql回滚到指定版本'
        ],
    ];

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
\033[33m Usage: app/console [options] [args...] \033[0m

";

    public function __construct($argv)
    {
        $this->argv = $argv;

        $this->mergeConfig();
    }

    /**
     * run the console
     *
     */
    public function run()
    {   
        $this->checkArgv();

        printf($this->help);
        foreach ($this->options as $command => $option) {
            $option['args'] = isset($option['args']) ? $option['args'] : '';
            printf("%-70s %-s".PHP_EOL, "\033[32m ".$command."\033[0m"."\033[33m ".$option['args']."\033[0m", $option['help']);
        }
    }

    /**
     * 检查输入的参数与命令
     *
     */
    protected function checkArgv()
    {
        $argv = $this->argv;
        if (!isset($argv[1])) return;
        $options = $this->options;
        if (!isset($options[$argv[1]]['command'])) {

            $this->help = "\033[31m错误的命令！\033[0m".PHP_EOL;
            return;
        }

        $command = new $options[$argv[1]]['command'];
        $command->setArgv($argv);
        $command->init();
        die;
    }

    private function mergeConfig()
    {
        $commands = \Config::get("app::console_commands");
        $options = [];
        $helps = "";
        foreach ($commands as $command => $option) {
            $options[$command] = $option;
        }

        $this->options = array_merge($this->options, $options);

        $this->help = $this->help.$helps;
    }
}
