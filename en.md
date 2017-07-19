# Group-Co  

[![Build Status](https://travis-ci.org/fucongcong/Group-Co.svg?branch=master)](https://travis-ci.org/fucongcong/Group-Co)
[![Code Climate](https://codeclimate.com/github/fucongcong/Group-Co/badges/gpa.svg)](https://github.com/fucongcong/Group-Co)

#### Introduction
- Use Coroutine(generators).Write async code in sync style.
- Use [Swoole](https://github.com/swoole/swoole-src).
- For Api or Http Server.

#### * async,coroutine
#### * soa。parallel、serial call。The server uses AsyncTask for asynchronous processing, merges data and returns it。
#### * AsyncLog,AsyncFile,AsyncMysql,AsyncRedis
#### * MysqlPool,RedisPool
#### * Mysql Transaction
#### * Twig,Doctrine
#### * PhpUnit for Test

##### Requirements
- [hiredis](https://github.com/redis/hiredis)
- redis
- mysql
- php5.6
- swoole >=1.9.15 (compile with ./configuire --enable-async-redis)

##### Install
- clone project
- run => composer install
- make dir => runtime
- start http server => php server.php
- visit http://localhost:9777/ ,it's work!

##### Usage
- start http server => php server.php
- relaod htt pserver => php server.php -s reload
- close http server => php server.php -s stop
- start one service => app/service user
- reload one service => app/service user reload
- close one service => app/service user stop

##### Careful
- 1.can't set swoole param => max_request。
- 2.Memory release issues, local static variables, and global variables are released。

##### Base Service
- AsyncMysql
- AsyncRedis
- AsyncService
- AsyncLog
- AsyncFile
- Container
- Controller
- Config
- Event
- Route
- Request
- Response
- StaticCache
- Sync
  - Container
  - Console
  - FileCache
  - RedisCache
  - StaticCache
  - Log
  - Dao
  - Service
- Test

#### Features

##### Serial call

```php
    
    $start = microtime(true);
    //set 2s timeout
    service("user")->setTimeout(2);
    $users = (yield service("user")->call("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]));
    dump($users);

```

##### Serial call(with service center)

```php
    
    $start = microtime(true);
    //set 2s timeout
    $service = (yield service_center("User"));
    $service->setTimeout(2);
    $users = (yield $service->call("User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]));
    dump($users);

```

##### Parallel call

```php

    $start = microtime(true);
    //set 2s timeout
    service("user")->setTimeout(2);

    $callId1 = service("user")->addCall("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]);
    $callId2 = service("user")->addCall("User\User::getUser", ['id' => 1]);
    $res = (yield service("user")->multiCall());

    dump($res[$callId1]);
    dump($res[$callId2]);
    dump(microtime(true) - $start);
    
```

##### Parallel call(with service center)

```php

    $start = microtime(true);
    //set 2s timeout
    $service = (yield service_center("User"));
    $service->setTimeout(2);

    $callId1 = $service->addCall("User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]);
    $callId2 = $service->addCall("User::getUser", ['id' => 1]);
    $res = (yield $service->multiCall());

    dump($res[$callId1]);
    dump($res[$callId2]);
    dump(microtime(true) - $start);
    
```

##### Async redis(default use pool)

```php
    
    use AsyncRedis;

    //unable pool
    \AsyncRedis::enablePool(false);
    //enable pool
    \AsyncRedis::enablePool(true);
    //set timeout
    AsyncRedis::setTimeout(2);

    yield AsyncRedis::set('foo', 'bar');
    dump(yield AsyncRedis::get('foo'));
    $user = json_encode(['foo' => 'bar']);
    yield AsyncRedis::hSet('user', 1, $user);
    dump(yield AsyncRedis::hGet('user', 1));
    
```

##### Async mysql(default use pool)

```php
    
    use AsyncMysql;
    
    //configure pool num => config/database.php
    AsyncMysql::query($sql, $usePool = true);

    //set timeout
    AsyncMysql::setTimeout(2);

    $res = (yield AsyncMysql::query("INSERT INTO `user` (`id`, `mobile`, `password`) VALUES (NULL, '18768122222', '11111')"));
    //fail return false   
    if ($res) {
        $result = $res->getResult();
        $affectedRows = $res->getAffectedRows();
        $id = $res->getInsertId();
    }
    
```

##### Async mysql transaction

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

##### Async Log

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


##### Async File

```php
    
    use AsyncFile;

    $content = (yield AsyncFile::read(__ROOT__."runtime/test.txt"));

    $res = (yield AsyncFile::write(__ROOT__."runtime/test.txt", "hello wordls!"));

    $res = (yield AsyncFile::write(__ROOT__."runtime/test.txt", "hello wordls!", FILE_APPEND));

```

##### Exception

```php
    
    //if not catch，the frame will catchs，return 500 server error response。In dev env, will return trace。
    try {
        throw new \Exception("Error Processing Request", 1); 
        //yield throwException(new \Exception("Error Processing Request", 1));
    } catch (\Exception $e) {
        echo  $e->getMessage();
    }

```

##### Demo
- configuring Nginx，=> doc/nginx.md. Configuring hosts
- configuring config/service.php,database.php
- exec => app/console sql:migrate 
- start node_center service => app/service node_center (config/service.php => node_center)
- start user service => app/service user
- see node info => groupco.com/admin，publish user service
- visit servername => groupco.com/demo

##### Not Used Service Center
- Set config/service.php => 'node_center' param empty;
- Notes config/app.php => 'swoole_process' => 'src\Admin\Process\HeartbeatProcess'
- Don't use service_center() func.

##### Service Center

![服务治理](soa.png)

##### License MIT
##### Thanks Swoole