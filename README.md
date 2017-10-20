# Group-Co	

[![Build Status](https://travis-ci.org/fucongcong/co-framework.svg?branch=master)](https://travis-ci.org/fucongcong/Group-Co)
[![Code Climate](https://codeclimate.com/github/fucongcong/co-framework/badges/gpa.svg)](https://github.com/fucongcong/Group-Co)

### 为什么写这个框架？
- 利用协程特性以同步方式来编写异步代码，增强可读性。
- 将swoole的异步特性与传统框架的MVC相结合。
- 可以用作api也可以用作http server,rpc server.

### 特性
- 全异步协程调度，支持高并发
- 异步TCP，HTTP客户端
- 异步日志
- 异步文件读写
- 异步Mysql
- 异步Mysql事务处理
- 异步Redis
- 支持Mysql连接池,Redis连接池
- SOA服务化调用，内部封装完整的RPC通信，服务端采用异步Task处理后合并数据并返回。
- 异步TCP客户端支持并行、串行调用
- 支持EOF结束符协议、自定义网络通信协议，支持json化、php序列化包体，支持gzip。
- Twig、Doctrine支持视图、服务数据层
- 单元测试覆盖

### 环境依赖与使用
- [环境依赖与使用,戳这里](https://github.com/fucongcong/Group-Co/blob/master/start.md)

### 异步Tcp客户端

```php

    use AsyncTcp;

    $tcp = new AsyncTcp('127.0.0.1', 9501);
    $tcp->setTimeout(2);
    //串行发送
    $res = (yield $tcp->call('hello server!'));
    $res = (yield $tcp->call('hello server!'));

    //并行发送数据包
    $tcp->addCall('hello server1!');
    $tcp->addCall('hello server2!');
    $res = (yield $tcp->multiCall());

```
##### Tips(如果使用tcp异步客户端和其他服务端通信)
- tcp客户端的数据包格式可在config/app.php中配置.
- protocol为buf时，是按包头+包体封装数据包的，包头为4个字节，存放包体的长度，解包时同样也是按包头+包体解包，所以服务端send数据时也要按同样规则封包。
- protocol为eof时，是按'\r\n'结束符封装数据包的，解包时同样也是按'\r\n'解包，所以服务端send数据时也要按'\r\n'结束符封装数据包。
- protocol为空的话，不封装数据包。在应答式响应中可以使用，否则会出现粘包现象。(框架内部封装的service为该模式)

### 异步Http客户端

```php

    use AsyncHttp;

    //直接使用域名, get方式
    $http = new AsyncHttp('http://groupco.com');
    //设置2s超时
    $http->setTimeout(2);
    //$http->setCookies(['token' => 'xxxx']);
    $res = (yield $http->get('/'));

    //也可以通过ip:port方式
    $http = new AsyncHttp('http://127.0.0.1:80');
    $http->setHost('groupco.com');
    $res = (yield $http->get('/user', ['id' => 1]));

    //使用https, post方式
    $http = new AsyncHttp('https://groupco.com');
    $res = (yield $http->post('/test', ['postId' => 52]));

```

### 异步redis(默认使用连接池)

```php
    
    use AsyncRedis;

    //关闭连接池
    AsyncRedis::enablePool(false);
    //开启连接池
    AsyncRedis::enablePool(true);
    //设置超时时间
    AsyncRedis::setTimeout(2);

    yield AsyncRedis::set('foo', 'bar');
    dump(yield AsyncRedis::get('foo'));
    $user = json_encode(['foo' => 'bar']);
    yield AsyncRedis::hSet('user', 1, $user);
    dump(yield AsyncRedis::hGet('user', 1));
    
```

### 异步mysql(默认使用连接池)

```php
    
    use AsyncMysql;
    
    //第二个参数设为false将不会使用连接池中的资源，默认都会从连接池中取，配置连接池数量 => config/database.php
    AsyncMysql::query($sql, $usePool = true);

    //设置超时时间
    AsyncMysql::setTimeout(2);

    $res = (yield AsyncMysql::query("INSERT INTO `user` (`id`, `mobile`, `password`) VALUES (NULL, '18768122222', '11111')"));
    //失败返回false   
    if ($res) {
        $result = $res->getResult();
        $affectedRows = $res->getAffectedRows();
        $id = $res->getInsertId();
    }
    
```

### 异步mysql事务处理

```php
    
    use AsyncMysql;
    
    public function test()
    {
        try {
            yield AsyncMysql::begin();

            $res = (yield $this->doTrans());
            if ($res === false) {
                throw new \Exception("need roll back");
            }

            yield AsyncMysql::commit();
        } catch (\Exception $e) {
            yield AsyncMysql::rollback();
        }
    }

    public function doTrans()
    {
        $res = (yield AsyncMysql::query("INSERT INTO `user` (`id`, `mobile`, `password`) VALUES (NULL, '187681343332', '11111')"));
        if ($res) {
            $result = $res->getResult();
            $affectedRows = $res->getAffectedRows();
            $id = $res->getInsertId();
            $res = (yield AsyncMysql::query("SELECT * FROM `user` WHERE id = {$id}"));
            $res = (yield AsyncMysql::query("SELECT * FROM `user`"));
            $res = (yield AsyncMysql::query("DELETE FROM `user` WHERE id = {$id}", false));
        }

        yield true;
    }
```

### 异步Log

```php
    
    use AsyncLog;

    yield AsyncLog::info('hello world');

    yield AsyncLog::debug('test debug', ['foo' => 'bar']);

    yield AsyncLog::notice('hello world',[], 'group.com');

    yield AsyncLog::warning('hello world');

    yield AsyncLog::error('hello world');

    yield AsyncLog::critical('hello world');

    yield AsyncLog::alert('hello world');

    yield AsyncLog::emergency('hello world');
    
```


### 异步文件读写

```php
    
    use AsyncFile;

    $content = (yield AsyncFile::read(__ROOT__."runtime/test.txt"));

    $res = (yield AsyncFile::write(__ROOT__."runtime/test.txt", "hello wordls!"));

    $res = (yield AsyncFile::write(__ROOT__."runtime/test.txt", "hello wordls!", FILE_APPEND));

```

### 异常处理

```php
    
    //如果在业务层不catch，框架层会捕捉，并返回一个500的server error响应。如果在开发环境会返回一个500的具体错误的trace响应。
    try {
        throw new \Exception("Error Processing Request", 1); 
        //yield throwException(new \Exception("Error Processing Request", 1));
    } catch (\Exception $e) {
        echo  $e->getMessage();
    }

```

### SOA客户端，串行调用(不使用服务中心)

```php
    
    $start = microtime(true);
    //设置2秒超时
    service("user")->setTimeout(2);
    $users = (yield service("user")->call("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]));
    dump($users);

```

### SOA客户端，串行调用(使用服务中心)

```php
    
    $start = microtime(true);
    //设置2秒超时
    $service = (yield service_center("User"));
    $service->setTimeout(2);
    $users = (yield $service->call("User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]));
    dump($users);

```

### SOA客户端，并行调用(不使用服务中心)

```php

    $start = microtime(true);
    //设置2秒超时
    service("user")->setTimeout(2);

    $callId1 = service("user")->addCall("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]);
    $callId2 = service("user")->addCall("User\User::getUser", ['id' => 1]);
    $res = (yield service("user")->multiCall());

    dump($res[$callId1]);
    dump($res[$callId2]);
    dump(microtime(true) - $start);
    
```

### SOA客户端，并行调用(使用服务中心，只能针对同一服务模块)

```php

    $start = microtime(true);
    //设置2秒超时
    $service = (yield service_center("User"));
    $service->setTimeout(2);

    $callId1 = $service->addCall("User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]);
    $callId2 = $service->addCall("User::getUser", ['id' => 1]);
    $res = (yield $service->multiCall());

    dump($res[$callId1]);
    dump($res[$callId2]);
    dump(microtime(true) - $start);
    
```

### 使用服务治理中心
- 设置config/service.php中的node_center地址
- 开启config/app.php中swoole_process选项的'src\Admin\Process\HeartbeatProcess'
- 重启user服务 => app/service user reload
- 启动node_center服务 => app/service node_center
- 重启http server
- 访问 /admin 路由，开始服务治理
- 注意使用service_center()方法获取服务模块
- 使用监控Monitor服务 app/service monitor

#### 服务治理示意图

![服务治理](soa.png)


#### Monitor监控上报

```php

    //在框架层，调用servcie时，会抛出KernalEvent::SERVICE_CALL事件，你可以监听该事件，做数据上报处理，请以异步方式上报，例如：

    <?php

    namespace src\Web\Listeners;

    use Listener;
    use Event;

    class ServiceCallListener extends Listener
    {
        public function setMethod()
        {
            return 'onServiceCall';
        }

        public function onServiceCall(Event $event)
        {
            $data = $event->getProperty();
            $cmd = $data['cmd'];
            $calltime = $data['calltime'];

            //上报监控平台
            //do something
        }
    }
```
##### License MIT
##### 感谢Swoole

### 以下服务”使用方式“与Group框架使用方式一致。所以直接给出了Group框架使用时的文档链接

### 异步Http server还可以使用的服务
- [Config](https://fucongcong.gitbooks.io/group-doc/content/configpei-zhi.html)
- [Event](https://fucongcong.gitbooks.io/group-doc/content/eventshi-jian.html)
- [Route](https://fucongcong.gitbooks.io/group-doc/content/lu-you.html)
- [Request](https://fucongcong.gitbooks.io/group-doc/content/requestqing-qiu.html)
- [Response](https://fucongcong.gitbooks.io/group-doc/content/responsexiang-ying.html)
- [StaticCache](https://fucongcong.gitbooks.io/group-doc/content/filecachewen-jian-huan-cun.html)

### Service还可以使用的服务
- [StaticCache](https://fucongcong.gitbooks.io/group-doc/content/filecachewen-jian-huan-cun.html)
- [Console](https://fucongcong.gitbooks.io/group-doc/content/consolekong-zhi-tai.html)
- [FileCache](https://fucongcong.gitbooks.io/group-doc/content/filecachewen-jian-huan-cun.html)
- [RedisCache](https://fucongcong.gitbooks.io/group-doc/content/cachehuan-cun.html)
- [Log](https://fucongcong.gitbooks.io/group-doc/content/logri-zhi.html)
- [Dao](https://fucongcong.gitbooks.io/group-doc/content/servicefu-wu.html)
- [Service](https://fucongcong.gitbooks.io/group-doc/content/servicefu-wu.html)


### TODO
- 自定义protocol。
- 异步大文件读取，目前只能读取<4M的文件

