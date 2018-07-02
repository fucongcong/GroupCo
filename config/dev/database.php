<?php
return
[   
    //redis null
    'cache' => 'redis',

    'redis' => [

        //redis连接池数量
        'maxPool' => 5,

        //redis连接超时时间
        'timeout' => 5,
    
        'default' => [
            'host'     => '127.0.0.1',
            'port'     => 6379,
            'prefix'   => 'co:',
            'auth'     => '',
            'connect'  => 'persistence'
        ],

        //集群模式只在service下使用有效，异步redis中并不适用
        'cluster' => false,
        'cluster_options' => [
            'connect_timeout' => 2,
            'read_timeout' => 2,
            'connect'  => 'persistence',
            'prefix' => 'co:',
        ],
        'clusters' => [
            'default' => [
                'host' => '127.0.0.1',
                'port'     => 6382,
            ],
            'redis1' => [
                'host' => '127.0.0.1',
                'port'     => 6380,
            ],
            'redis2' => [
                'host' => '127.0.0.1',
                'port'     => 6381,
            ],
        ],
    ],

    //mysql连接池数量
    'maxPool' => 5,

    //mysql连接超时时间
    'timeout' => 5,

    //默认可以不开启读写配置，读写配置可以配置多个
    'pdo' => [

        'default' => [

            "driver" => "pdo_mysql",

            "host" => "127.0.0.1",

            "port" => "3306",

            "dbname" => "Demo",

            "user" => "root",

            "password" => "123",

            "charset" => "utf8",
        ],

        // 'write' => [

        //     'master1' => [

        //         "driver" => "pdo_mysql",

        //         "host" => "127.0.0.1",

        //         "port" => "3306",

        //         "dbname" => "Group1",

        //         "user" => "root",

        //         "password" => "123",

        //         "charset" => "utf8",
        //     ],

        //     'master2' => [

        //         "driver" => "pdo_mysql",

        //         "host" => "127.0.0.1",

        //         "port" => "3306",

        //         "dbname" => "Group2",

        //         "user" => "root",

        //         "password" => "123",

        //         "charset" => "utf8",
        //     ],
        // ],

        // 'read' => [

        //     'slave1' => [

        //         "driver" => "pdo_mysql",

        //         "host" => "127.0.0.1",

        //         "port" => "3306",

        //         "dbname" => "Group3",

        //         "user" => "root",

        //         "password" => "123",

        //         "charset" => "utf8",
        //     ],

        //     'slave2' =>  [

        //         "driver" => "pdo_mysql",

        //         "host" => "127.0.0.1",

        //         "port" => "3306",

        //         "dbname" => "Group4",

        //         "user" => "root",

        //         "password" => "123",

        //         "charset" => "utf8",
        //     ],
        // ],
    ],
];
