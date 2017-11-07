### 异步Tcp客户端

#### 串行发包

```php
    use AsyncTcp;

    $tcp = new AsyncTcp('127.0.0.1', 9501);
    $tcp->setTimeout(2);
    //串行发送
    $res = (yield $tcp->call('hello server!'));
    $res = (yield $tcp->call('hello server!'));
```

#### 并行发包

```php
    use AsyncTcp;

    $tcp = new AsyncTcp('127.0.0.1', 9501);
    $tcp->setTimeout(2);
    //并行发送数据包
    $tcp->addCall('hello server1!');
    $tcp->addCall('hello server2!');
    $res = (yield $tcp->multiCall());
```

##### 

##### Tips\(如果使用tcp异步客户端和其他服务端通信\)

* tcp客户端的数据包格式可在config/app.php中配置.
* protocol为buf时，是按包头+包体封装数据包的，包头为4个字节，存放包体的长度，解包时同样也是按包头+包体解包，所以服务端send数据时也要按同样规则封包。
* protocol为eof时，是按'\r\n'结束符封装数据包的，解包时同样也是按'\r\n'解包，所以服务端send数据时也要按'\r\n'结束符封装数据包。
* protocol为空的话，不封装数据包。在应答式响应中可以使用，否则会出现粘包现象。\(框架内部封装的service为该模式\)

### 



