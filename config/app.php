<?php
return [
    // prod|dev
    'environment' => 'dev',

    //只有在dev环境下才生效。tip: swoole http server下无法正常debug
    'debug' => true,

    //zh|en|fr...
    'locale' => 'zh',

    //时区
    'timezone' => 'Asia/Shanghai',

    //类的映射
    'aliases' => [
        //like  'demo'       => 'src\Service\demo',
    ],

    'onWorkStartServices' => [
        'Group\ASync\Pool\MysqlPoolServiceProvider',
        'Group\ASync\Pool\RedisPoolServiceProvider',
    ],

    'onRequestServices' => [
        //如果做api服务,可以不加载twig
        'Group\Controller\TwigServiceProvider',
    ],

    //需要实例化的单例
    'singles' => [
        //like  'demo'       => 'src\demo\demo',
    ],

    //扩展console命令行控制台
    'console_commands' => [
        'log.clear' => [
            'command' => 'src\Web\Command\LogClearCommand', //执行的类
            'help' => '清除日志', //提示
        ],
    ],

    'swoole_host' => '127.0.0.1',

    'swoole_port' => 9777,

    'swoole_setting' => [
        //日志
        'daemonize' => true,
        'log_file' => 'runtime/error.log',
        'worker_num' => 2,    //worker process num
        'backlog' => 256,   //listen backlog
        'heartbeat_idle_time' => 30,
        'heartbeat_check_interval' => 10,
        'dispatch_mode' => 3, 
    ],

    //在启动时可以添加用户自定义的工作进程,必须是swoole_process
    'swoole_process' => [
        'src\Admin\Process\HeartbeatProcess',
    ],
];