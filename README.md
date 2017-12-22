# Group-Co

[![Build Status](https://travis-ci.org/fucongcong/co-framework.svg?branch=master)](https://travis-ci.org/fucongcong/Group-Co)  
[![Code Climate](https://codeclimate.com/github/fucongcong/co-framework/badges/gpa.svg)](https://github.com/fucongcong/Group-Co)

### 为什么写这个框架？

* 利用协程特性以同步方式来编写异步代码，增强可读性。
* 将swoole的异步特性与传统框架的MVC相结合。
* 可以用作api也可以用作http server,rpc server.
* 目前实现了以Zookeeper、Redis、Mysql为注册中心的服务化治理.

### 如何使用，与传统框架的区别？

* 框架基本使用与传统框架基本一致，路由，控制器，服务层，数据层。
* 在异步调用的地方需要以yield关键词来触发协程切换

### 特性

* 全异步协程调度，支持高并发
* 异步TCP，HTTP客户端
* 异步日志
* 异步文件读写
* 异步Mysql
* 异步Mysql事务处理
* 异步Redis
* 支持Mysql连接池,Redis连接池
* SOA服务化调用，内部封装完整的RPC通信，服务端采用异步Task处理后合并数据并返回。
* 异步TCP客户端支持并行、串行调用
* 支持EOF结束符协议、自定义网络通信协议，支持json化、php序列化包体，支持gzip。
* Twig、Doctrine支持视图、服务数据层
* 单元测试覆盖

### 文档总览

* 快速开始
  * [环境依赖](doc/yin-yan/huan-jing-yi-lai.md)
  * [启动项目](doc/yin-yan/qi-dong-xiang-mu.md)
* 异步服务
  * [异步Tcp客户端](doc/kuang-jia-fu-wu/yi-bu-tcp-ke-hu-duan.md)
  * [异步Http客户端](doc/kuang-jia-fu-wu/yi-bu-http-ke-hu-duan.md)
  * [异步Redis客户端](doc/kuang-jia-fu-wu/sd.md)
  * [异步Mysql客户端](doc/kuang-jia-fu-wu/yi-bu-mysql-ke-hu-duan.md)
  * [异步Log日志](doc/kuang-jia-fu-wu/yi-bu-log.md)
  * [异步文件读写](doc/kuang-jia-fu-wu/yi-bu-wen-jian-du-xie.md)
  * [异常Exception](doc/kuang-jia-fu-wu/yi-chang-exception.md)
* 服务中心
  * [服务治理流程](doc/fu-wu-zhong-xin/fu-wu-zhi-li-liu-cheng.md)
  * [注册中心](doc/fu-wu-zhong-xin/zhu-ce-zhong-xin.md)
  * [服务调用](doc/fu-wu-zhong-xin/yi-bu-http-server-zhong-shi-yong-fu-wu.md)
  * [服务调用监控](doc/fu-wu-zhong-xin/fu-wu-diao-yong-jian-kong.md)
  * [调试模式](doc/fu-wu-zhong-xin/diao-shi-mo-shi.md)
* 基础服务
  * [Config配置类](doc/ji-chu-fu-wu/config.md)
  * [StaticCache静态缓存类](doc/ji-chu-fu-wu/staticcache.md)
  * [Route路由类](doc/ji-chu-fu-wu/routelu-you-lei.md)
  * [Controller控制器类](doc/ji-chu-fu-wu/controllerkong-zhi-qi-lei.md)
  * [View视图类](doc/ji-chu-fu-wu/viewshi-tu-lei.md)
  * [Request请求类](doc/ji-chu-fu-wu/requestqing-qiu-lei.md)
  * [Response响应类](doc/ji-chu-fu-wu/responsexiang-ying-lei.md)
  * [Event事件类](doc/ji-chu-fu-wu/eventshi-jian-lei.md)
  * [Listener监听类](doc/ji-chu-fu-wu/listenerjian-ting-lei.md)
  * [Subscriber多事件监听](doc/ji-chu-fu-wu/subscriberduo-shi-jian-jian-ting.md)
  * [EventDispatcher事件调度](doc/ji-chu-fu-wu/eventdispatchershi-jian-diao-du-lei.md)
* 同步服务\(用于服务开发\)
  * [Service](doc/tong-bu-fu-wu/service.md)
  * [Dao](doc/tong-bu-fu-wu/dao.md)
  * [Cache](doc/tong-bu-fu-wu/rediscache.md)
  * [Log日志类](doc/tong-bu-fu-wu/logri-zhi-lei.md)
  * [FileCache文件缓存类](doc/tong-bu-fu-wu/filecachewen-jian-huan-cun-lei.md)
* [控制台](doc/kong-zhi-tai/kong-zhi-tai.md)

### 环境依赖

* [**hiredis**](https://github.com/redis/hiredis)**（redis异步库）**
* **redis**
* **mysql**
* **php &gt;5.6 或者 php &gt; 7.0**
* **swoole &gt;=1.9.17\(建议升级到最新版本\) \(在编译swoole时加入--enable-async-redis，开启异步redis客户端, --enable-openssl开启openssl支持,--with-openssl-dir指定你的openssl目录\)**

> ##### 注：openssl是用于http异步客户端抓取https网址时依赖的模块，可以选择性开启

### 启动项目\(请先完成环境依赖安装\)

1. 克隆项目
2. 执行 =&gt; composer install (如果安装很慢，可以使用国内镜像，但是镜像包会有延迟)
3. 新建一个runtime目录，用于存放日志等cache文件
4. 配置config中的database配置文件
5. 设置config/service.php中的registry\_address.目前只支持redis、mysql作为注册中心
6. 启动http server =&gt; php server.php
7. 访问 [http://localhost:9777/](http://localhost:9777/) 开始异步协程之旅

#### Demo(将用nginx做一次反向代理资源文件)

* 修改配置nginx，见doc/nginx.md,配置hosts
* 配置config中的service配置文件
* 执行脚本 =&gt; app/console sql:migrate 
* 启动user服务 =&gt; app/service user
* 使用监控Monitor服务 app/service monitor
* 还可以启动其他服务，自行配置
* 访问配置的servername =&gt; groupco.com/demo 即可

#### 更新代码

* 执行 =&gt; composer update

#### 使用

* 启动http server =&gt; php server.php
* 热重启http pserver =&gt; php server.php -s reload
* 关闭http server =&gt; php server.php -s stop
* 重启http server =&gt; php server.php -s restart
* 启动某个服务 =&gt; app/service user
* 热重启某个服务 =&gt; app/service user reload
* 关闭某个服务 =&gt; app/service user stop

#### 特别注意

* 1.额外内存释放的问题，局部静态变量，全局变量的释放。
* 2.断线重连机制内部已封装\(在执行sql时如果出现长连接已失效，将尝试3次重连操作\)。

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

### 异步Http客户端

#### Get方式

1.使用域名形式

```php
    use AsyncHttp;

    //直接使用域名, get方式
    $http = new AsyncHttp('http://groupco.com');
    //设置2s超时
    $http->setTimeout(2);
    //$http->setCookies(['token' => 'xxxx']);
    $res = (yield $http->get('/'));
```

2.使用ip:port形式

```php
    use AsyncHttp;

    //也可以通过ip:port方式
    $http = new AsyncHttp('http://127.0.0.1:80');
    $http->setHost('groupco.com');
    $res = (yield $http->get('/user', ['id' => 1]));
```

#### Post方式

1.使用域名形式

```php
    use AsyncHttp;

    //使用https, post方式
    $http = new AsyncHttp('https://groupco.com');
    $res = (yield $http->post('/test', ['postId' => 52]));
```

2.使用ip:port形式

```php
    use AsyncHttp;

    //也可以通过ip:port方式
    $http = new AsyncHttp('http://127.0.0.1:80');
    $http->setHost('groupco.com');
    $res = (yield $http->post('/test', ['postId' => 52]));
```

> ##### 注：若请求https地址，需要在编译swoole时开启openssl

### 异步Mysql客户端

#### AsyncMysql::query\($sql, $usePool = true\)

> 第二个参数设为false将不会使用连接池中的资源，默认都会从连接池中取，配置连接池数量 =&gt; config/database.php

##### 具体使用

        use AsyncMysql;

        //设置超时时间
        AsyncMysql::setTimeout(2);

        $res = (yield AsyncMysql::query("INSERT INTO `user` (`id`, `mobile`, `password`) 
        VALUES (NULL, '18768122222', '11111')"));

        //失败返回false   
        if ($res) {
            $result = $res->getResult();
            $affectedRows = $res->getAffectedRows();
            $id = $res->getInsertId();
        }

#### 异步Mysql事务处理

> 与传统事务一样使用，只是需要加上yield关键词，以异步方式调用

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
            $res = (yield AsyncMysql::query("INSERT INTO `user` (`id`, `mobile`, `password`) 
            VALUES (NULL, '187681343332', '11111')"));

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

### 异步Redis客户端

#### 连接池（连接池默认开启）

```php
    use AsyncRedis;

    //关闭连接池
    AsyncRedis::enablePool(false);
    //开启连接池
    AsyncRedis::enablePool(true);
```

#### 使用AsyncRedis

```php
    use AsyncRedis;

    //设置超时时间
    AsyncRedis::setTimeout(2);

    yield AsyncRedis::set('foo', 'bar');
    dump(yield AsyncRedis::get('foo'));

    $user = json_encode(['foo' => 'bar']);
    yield AsyncRedis::hSet('user', 1, $user);
    dump(yield AsyncRedis::hGet('user', 1));
```

#### 修改配置信息config/database.php：

```
    'redis' => [

        //redis连接池数量
        'maxPool' => 5,

        //redis连接超时时间
        'timeout' => 5,

        'default' => [
            'host'     => '127.0.0.1',
            'port'     => 6379,
            'prefix'   => 'group_',
            'auth'     => '',
            'connect'  => 'persistence'
        ],

    ],
```

### 

### 异步Log日志

```
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

#### 读文件

```
    use AsyncFile;

    $content = (yield AsyncFile::read(__ROOT__."runtime/test.txt"));
```

#### 写文件

```
    $res = (yield AsyncFile::write(__ROOT__."runtime/test.txt", "hello wordls!"));

    $res = (yield AsyncFile::write(__ROOT__."runtime/test.txt", "hello wordls!", FILE_APPEND));
```

> 目前仅支持小于4M的文件

### 异常Exception

#### 以传统的try，catch抓取异常

> ##### 如果在业务层不catch，框架层会捕捉，并返回一个500的server error响应。
>
> ##### 如果在开发环境会返回一个500的具体错误的trace响应。

```
    try {
        throw new \Exception("Error Processing Request", 1); 
        //yield throwException(new \Exception("Error Processing Request", 1));
    } catch (\Exception $e) {
        echo  $e->getMessage();
    }
```

### 服务治理流程

![](soa.png)

### 注册中心

#### 设置注册中心

修改config/service.php中的registry\_address.目前只支持Redis、Mysql注册中心

##### Redis注册中心

```
    'registry_address' => 'redis://127.0.0.1:6379'
```

##### Mysql注册中心

```
    //mysql注册中心，开启后，请执行doc/mysql-registry.sql中的sql，创建2张表
    'registry_address' => 'mysql://127.0.0.1:3306?dbname=Demo&user=root&password=123',
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

### 在异步HTTP SERVER中使用服务

#### 全局方法service\_center\(\)

使用service\_center\($service\)获取服务地址,然后使用call\(\)方法调用公开的服务方法

```
    $service = (yield service_center('User'));
    $user = (yield $service->call("User::getUser", ['id' => $userId]));
```

> 上面的代码会调用src/Service/User/Service/Impl/UserServiceImpl中的getUser方法

#### 串行调用

```
    //设置2秒超时
    $service = (yield service_center("User"));
    $service->setTimeout(2);
    $users = (yield $service->call("User::getUsersCache", ['ids' => [1, 2]]));
    $users2 = (yield $service->call("User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]));
```

#### 并行调用（只能针对同一服务模块）

```
    $service = (yield service_center("User"));
    $service->setTimeout(2);

    $callId1 = $service->addCall("User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]);
    $callId2 = $service->addCall("User::getUser", ['id' => 1]);
    $res = (yield $service->multiCall());

    dump($res[$callId1]);
    dump($res[$callId2]);
```

#### 全局方法service\(\)

使用service\($serverName\)获取服务地址,然后使用call\(\)方法调用公开的服务方法.

service\(\)不会通过注册中心发现服务

```
    service("user")->setTimeout(2);
    $users = (yield service("user")->call("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5]]));
```

#### 串行调用

```
    service("user")->setTimeout(2);
    $users = (yield service("user")->call("User\User::getUsersCache", ['ids' => [1, 2, 3, 4]]));
    $users2 = (yield service("user")->call("User\User::getUsersCache", ['ids' => [1, 2, 3, 4]]));
```

#### 并行调用

```
    service("user")->setTimeout(2);

    $callId1 = service("user")->addCall("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5]]);
    $callId2 = service("user")->addCall("User\User::getUser", ['id' => 1]);
    $res = (yield service("user")->multiCall());

    dump($res[$callId1]);
    dump($res[$callId2]);
```

### 服务调用监控

#### KernalEvent::SERVICE\_CALL事件

> 在框架层，调用servcie时，会抛出KernalEvent::SERVICE\_CALL事件，你可以监听该事件，做数据上报处理，请以异步方式上报

```
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

##### 具体可见Event基础服务使用



