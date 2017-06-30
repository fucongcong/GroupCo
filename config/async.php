<?php
//soa异步服务
return [

    'client' => [],

    //加密token，16位。可修改
    'encipher' => 'uoI49l^^M!a5&bZt',

    //配置swoole异步服务器
    'server' => [
        //可以配置多个server，注意请监听不同的端口。
        //serverName
        'user_service' => [

            'serv' => '0.0.0.0',
            'port' => 9519,
            //server配置，请根据实际情况调整参数,守护进程参数请不要设为true
            'config' => [
                //worker进程数量         
                'worker_num' => 10,
                //最大请求数，超过后讲重启worker进程
                'max_request' => 50000,
                //task进程数量
                'task_worker_num' => 20,
                //task进程最大处理请求上限，超过后讲重启task进程
                'task_max_request' => 50000,
                //打开EOF检测
                'open_eof_check' => true, 
                //设置EOF 防止粘包
                'package_eof' => "\r\n", 
                'open_eof_split' => true, //底层拆分eof的包
                //心跳检测,长连接超时自动断开，秒
                'heartbeat_idle_time' => 300,
                //心跳检测间隔，秒
                'heartbeat_check_interval' => 60,
                //1平均分配，2按FD取摸固定分配，3抢占式分配，默认为取模
                'dispatch_mode' => 3,
                //日志
                'log_file' => 'runtime/async/user_service.log',
                //默认设置为CPU核数,可不配置
                //'reactor_num' => 2,
                //超过此连接数后，将拒绝后续的tcp连接
                //'max_conn' => 10000,
                //其他配置详见swoole官方配置参数列表
            ], 
        ],
    ],
];
