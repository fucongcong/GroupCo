<?php
return [
    //加密token，16位。可修改
    'encipher' => 'uoI49l^^M!a5&bZt',

    //注册中心，如果不为空的话，在server启动时会起一个子进程订阅依赖的服务列表。
    'registry_address' => [
        'scheme' => 'redis',
        'host' => '127.0.0.1',
        'prefix'   => 'group:',
        'port' => 6379,
        'auth' => '',
    ],
    //mysql注册中心，开启后，请执行doc/mysql-registry.sql中的sql，创建2张表
    // 'registry_address' => [
    //     'scheme' => 'mysql',
    //     'host' => '127.0.0.1',
    //     'port' => 3306,
    //     'user' => 'root',
    //     'password' => '123',
    //     'dbname' => 'Demo'
    // ],
    // 'registry_address' => [
    //     'scheme' => 'zookeeper',
    //     'host' => '127.0.0.1',
    //     'port' => 2181,
    //     //集群模式
    //     //'url' => '127.0.0.1:2181,127.0.0.1:2182'
    // ],

    //配置service
    'server' => [
        'monitor' => [
            //本机当前内网ip
            'ip' => '127.0.0.1',
            'serv' => '0.0.0.0',
            'port' => 9517,
            'config' => [
                'daemonize' => true,        
                'worker_num' => 2,
                'max_request' => 50000,
                'task_worker_num' => 5,
                'task_max_request' => 50000,
                'heartbeat_idle_time' => 300,
                'heartbeat_check_interval' => 60,
                'dispatch_mode' => 3,
                'log_file' => 'runtime/service/monitor.log',
            ],
            'public' => 'Monitor',
            'process' => [
                //你可以使用框架封装的心跳检测进程
                'Group\Process\HeartbeatProcess',
            ],
        ],
        //可以配置多个server，注意请监听不同的端口。
        //serverName
        'user' => [
            //本机当前内网ip
            'ip' => '127.0.0.1',

            'serv' => '0.0.0.0',
            'port' => 9511,
            //server配置，请根据实际情况调整参数
            'config' => [
                'daemonize' => true,
                //worker进程数量         
                'worker_num' => 2,
                //最大请求数，超过后讲重启worker进程
                'max_request' => 50000,
                //task进程数量
                'task_worker_num' => 5,
                //task进程最大处理请求上限，超过后讲重启task进程
                'task_max_request' => 50000,
                //心跳检测,长连接超时自动断开，秒
                'heartbeat_idle_time' => 300,
                //心跳检测间隔，秒
                'heartbeat_check_interval' => 60,
                //1平均分配，2按FD取摸固定分配，3抢占式分配，默认为取模
                'dispatch_mode' => 3,
                //日志
                'log_file' => 'runtime/service/user.log',
                //其他配置详见swoole官方配置参数列表
            ],
            
            //公开哪些服务，如果不填默认公开所有服务
            'public' => 'User',
        ],
        'user_2' => [
            //本机当前内网ip
            'ip' => '127.0.0.1',
            'serv' => '0.0.0.0',
            'port' => 9521,
            'config' => [
                'daemonize' => true,        
                'worker_num' => 2,
                'max_request' => 50000,
                'task_worker_num' => 5,
                'task_max_request' => 50000,
                'heartbeat_idle_time' => 300,
                'heartbeat_check_interval' => 60,
                'dispatch_mode' => 3,
                'log_file' => 'runtime/service/user_2.log',
            ],
            'public' => 'User',
        ],
        'order' => [
            //本机当前内网ip
            'ip' => '127.0.0.1',
            'serv' => '0.0.0.0',
            'port' => 9520,
            'config' => [
                'daemonize' => true,        
                'worker_num' => 1,
                'max_request' => 50000,
                'task_worker_num' => 2,
                'task_max_request' => 50000,
                'heartbeat_idle_time' => 300,
                'heartbeat_check_interval' => 60,
                'dispatch_mode' => 3,
                'log_file' => 'runtime/service/order.log',
            ],
            'public' => 'Order',
        ],
    ],
];
