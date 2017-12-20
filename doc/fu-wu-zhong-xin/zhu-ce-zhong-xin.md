### 注册中心

#### 设置注册中心

修改config/service.php中的registry\_address.目前只支持Zookeeper、Redis、Mysql注册中心 

##### Zookeeper注册中心 
* [Zookeeper扩展安装](http://pecl.php.net/package/zookeeper)
```
    'registry_address' => [
        'scheme' => 'zookeeper',
        'host' => '127.0.0.1',
        'port' => 2181,
        //集群模式
        //'url' => '127.0.0.1:2181,127.0.0.1:2182'
    ],
```

##### Redis注册中心 

```
    'registry_address' => [
        'scheme' => 'redis',
        'host' => '127.0.0.1',
        'port' => 6379,
        'auth' => '',
    ],
```

##### Mysql注册中心 

```
    //mysql注册中心，开启后，请执行doc/mysql-registry.sql中的sql，创建2张表
    'registry_address' => [
        'scheme' => 'mysql',
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'password' => '123',
        'dbname' => 'Demo'
    ],
```

#### 设置依赖的服务

修改config/app.php的services.你需要将项目依赖的服务模块写入该数组，在server启动时，会单独起一个进程订阅每个服务

```
//依赖的服务模块 
    'services' => ["User", "Order", "Monitor"],
```

#### 启动服务

执行命令 app/service \[需要执行的server名称\]

```
app/service user
```

#### 监控服务

##### 框架层提供了'Group\Process\HeartbeatProcess'，心跳检测类来监控服务健康。你可以在服务配置中加入此进程来启动监控。

例：

```
        'monitor' => [
            //本机当前内网ip
            'ip' => '127.0.0.1',
            'serv' => '0.0.0.0',
            'port' => 9517,
            'config' => [
                //忽略
            ],
            'public' => 'Monitor',
            'process' => [
                //你可以使用框架封装的心跳检测进程
                'Group\Process\HeartbeatProcess',
            ],
        ],
```



