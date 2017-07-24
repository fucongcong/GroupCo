# Group-Co	

[![Build Status](https://travis-ci.org/fucongcong/co-framework.svg?branch=master)](https://travis-ci.org/fucongcong/Group-Co)
[![Code Climate](https://codeclimate.com/github/fucongcong/co-framework/badges/gpa.svg)](https://github.com/fucongcong/Group-Co)

#### 为什么写这个框架？
- 利用协程特性以同步方式来编写异步代码，增强可读性。
- 将swoole的异步特性与传统框架的MVC相结合。
- 可以用作api也可以用作http server。

#### * 异步协程调度，应对高并发
#### * SOA服务化调用，支持并行、串行调用、请求合并调用。服务端采用AsyncTask进行异步处理后合并数据并返回。
#### * 支持异步日志,异步文件读写,异步Mysql,异步Redis
#### * 支持Mysql连接池,Redis连接池
#### * Mysql事务处理
#### * Twig、Doctrine支持视图、服务数据层
#### * 单元测试覆盖

##### TODO
- http异步客户端
- Protocal Buffer的支持，目前已json方式序列化数据。
- 异步大文件读取，目前只能读取<4M的文件

##### 环境依赖
- [hiredis](https://github.com/redis/hiredis)
- redis
- mysql
- php5.6
- swoole >=1.9.15 (在编译swoole时加入--enable-async-redis，开启异步redis客户端)

##### 安装(请先完成环境依赖安装)
- 克隆项目
- 执行 => composer install
- 新建一个runtime目录，用于存放日志等cache文件
- 配置config中的database配置文件
- 启动http server => php server.php
- 访问 http://localhost:9777/ 开始异步协程之旅
- demo 查看，请继续一下步骤，将用nginx做一次反向代理资源文件
- 修改配置nginx，见doc/nginx.md,配置hosts
- 配置config中的service配置文件
- 执行脚本 => app/console sql:migrate 
- 启动user服务 => app/service user
- 还可以启动其他服务，自行配置
- 访问配置的servername => groupco.com/demo 即可

##### 使用服务治理中心
- 设置config/service.php中的node_center地址
- 开启config/app.php中swoole_process选项的'src\Admin\Process\HeartbeatProcess'
- 重启user服务 => app/service user reload
- 启动node_center服务 => app/service node_center
- 重启http server
- 访问 /admin 路由，开始服务治理
- 注意使用service_center()方法获取服务模块
- 使用监控Monitor服务 app/service monitor

##### 使用
- 启动http server => php server.php
- 热重启htt pserver => php server.php -s reload
- 关闭http server => php server.php -s stop
- 启动某个服务 => app/service user
- 热重启某个服务 => app/service user reload
- 关闭某个服务 => app/service user stop

##### 要注意的点
- 1.因为是异步的，无法设置swoole的max_request参数,stop 与reload的使用也会使部分请求失败。（解决：升级版本到>1.9.17）
- 2.格外内存释放的问题，局部静态变量，全局变量的释放。
- 3.断线重连机制内部已封装(在执行sql时如果出现长连接已失效，将尝试3次重连操作)。

##### 基础服务
- AsyncMysql
- AsyncRedis
- AsyncService
- AsyncLog
- AsyncFile
- Container
- Controller
- [Config](https://fucongcong.gitbooks.io/group-doc/content/configpei-zhi.html)
- [Event](https://fucongcong.gitbooks.io/group-doc/content/eventshi-jian.html)
- [Route](https://fucongcong.gitbooks.io/group-doc/content/lu-you.html)
- [Request](https://fucongcong.gitbooks.io/group-doc/content/requestqing-qiu.html)
- [Response](https://fucongcong.gitbooks.io/group-doc/content/responsexiang-ying.html)
- [StaticCache](https://fucongcong.gitbooks.io/group-doc/content/filecachewen-jian-huan-cun.html)
- Sync
  - Container
  - [Console](https://fucongcong.gitbooks.io/group-doc/content/consolekong-zhi-tai.html)
  - [FileCache](https://fucongcong.gitbooks.io/group-doc/content/filecachewen-jian-huan-cun.html)
  - [RedisCache](https://fucongcong.gitbooks.io/group-doc/content/cachehuan-cun.html)
  - [StaticCache](https://fucongcong.gitbooks.io/group-doc/content/filecachewen-jian-huan-cun.html)
  - [Log](https://fucongcong.gitbooks.io/group-doc/content/logri-zhi.html)
  - [Dao](https://fucongcong.gitbooks.io/group-doc/content/servicefu-wu.html)
  - [Service](https://fucongcong.gitbooks.io/group-doc/content/servicefu-wu.html)
- Test

#### 常用特性使用

##### 串行调用(不使用服务中心)

```php
    
    $start = microtime(true);
    //设置2秒超时
    service("user")->setTimeout(2);
    $users = (yield service("user")->call("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]));
    dump($users);

```

##### 串行调用(使用服务中心)

```php
    
    $start = microtime(true);
    //设置2秒超时
    $service = (yield service_center("User"));
    $service->setTimeout(2);
    $users = (yield $service->call("User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]));
    dump($users);

```

##### 请求合并(不使用服务中心)

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

##### 请求合并(使用服务中心，只能针对同一服务模块)

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

##### 异步redis(默认使用连接池)

```php
    
    use AsyncRedis;

    //关闭连接池
    \AsyncRedis::enablePool(false);
    //开启连接池
    \AsyncRedis::enablePool(true);
    //设置超时时间
    AsyncRedis::setTimeout(2);

    yield AsyncRedis::set('foo', 'bar');
    dump(yield AsyncRedis::get('foo'));
    $user = json_encode(['foo' => 'bar']);
    yield AsyncRedis::hSet('user', 1, $user);
    dump(yield AsyncRedis::hGet('user', 1));
    
```

##### 异步mysql(默认使用连接池)

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

##### 异步mysql事务处理

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

##### 异步Log

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


##### 异步文件读写

```php
    
    use AsyncFile;

    $content = (yield AsyncFile::read(__ROOT__."runtime/test.txt"));

    $res = (yield AsyncFile::write(__ROOT__."runtime/test.txt", "hello wordls!"));

    $res = (yield AsyncFile::write(__ROOT__."runtime/test.txt", "hello wordls!", FILE_APPEND));

```

##### 异常处理

```php
    
    //如果在业务层不catch，框架层会捕捉，并返回一个500的server error响应。如果在开发环境会返回一个500的具体错误的trace响应。
    try {
        throw new \Exception("Error Processing Request", 1); 
        //yield throwException(new \Exception("Error Processing Request", 1));
    } catch (\Exception $e) {
        echo  $e->getMessage();
    }

```

##### 服务治理示意图

![服务治理](soa.png)

##### License MIT
##### 感谢Swoole

##### Monitor监控上报

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