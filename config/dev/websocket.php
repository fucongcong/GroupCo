<?php 
return [
    //===============================websocket config==========================
    //单机配置
    'ws.serv' => '127.0.0.1',
    'ws.port' => '9527',
    //集群就启用注册中心
    //'ws.registry' => true,
    'ws.setting' => [
        //日志
        //'daemonize' => true,
        'log_file' => 'runtime/error.log',
        'log_level' => 5,
        'worker_num' => 4,    //worker process num
        'backlog' => 256,   //listen backlog
        'heartbeat_idle_time' => 30,
        'heartbeat_check_interval' => 10,
        'dispatch_mode' => 1, 
    ],
    //===============================websocket config==========================
];