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
        
        //'Group\Queue\QueueServiceProvider',
    ],

    'onRequestServices' => [
        //'Group\Redis\RedisServiceProvider',
        //'Group\Queue\QueueServiceProvider',
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

    //当使用swoole http server 时，需要指定host,port
    'swoole_host' => '127.0.0.1',
    'swoole_port' => 9777,
    'swoole_setting' => [
        //日志
        'log_file' => 'runtime/error.log',
        'worker_num' => 2,    //worker process num
        'backlog' => 256,   //listen backlog
        'max_request' => 20000,
        'heartbeat_idle_time' => 30,
        'heartbeat_check_interval' => 10,
        'dispatch_mode' => 3, 
    ],

];