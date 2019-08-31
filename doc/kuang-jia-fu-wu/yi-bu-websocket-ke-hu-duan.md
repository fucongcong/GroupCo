### 异步WebSocket客户端(在dev-master版本有效,release版本未开放)

#### 开启WebSocket连接池,在app.php中配置如下

```php
    //配置WebSocketPoolServiceProvider
    'onWorkStartServices' => [
        'Group\Async\Pool\WebSocketPoolServiceProvider',
    ],

    //配置其他信息
    'ws.serv' => '127.0.0.1',
    'ws.port' => '9527',
    //连接池大小
    'ws.maxPool' => 100,
    'ws.ssl'  => false,
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
```

#### 使用

```php
    //向指定的websocket服务端发送数据
    $res = (yield \AsyncWebSocket::send('127.0.0.1', 9527, json_encode($data)));
    //接收响应 若服务端不响应 默认5S超时
    dump($res);

```