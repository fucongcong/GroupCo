# Group-Co	

#### 异步协程，应对高并发

#### SOA服务化调用，支持并行、串行调用

#### 支持异步日志,异步Mysql,异步Redis

#### 支持数据库连接池

#### 强大的Twig、Doctrine支持视图、数据层

##### 串行调用

```php

	//串行
    $start = microtime(true);
    //设置2秒超时
    service("user_service")->setTimeout(2);
    $users = (yield service("user_service")->call("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]));
    dump($users);

```

##### 并行调用

```php

    //并行
    $start = microtime(true);
    $callId1 = service("user_service")->addCall("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]);
    $callId2 = service("user_service")->addCall("User\User::getUser", ['id' => 1]);
    $res = (yield service("user_service")->multiCall());
    dump($res[$callId1]);
    dump($res[$callId2]);
    dump(microtime(true) - $start);
    
```

##### 异步redis

```php

    //异步redis
    yield \Group\Cache\AsyncRedis::set('foo', 'bar');
    dump(yield \Group\Cache\AsyncRedis::get('foo'));
    $user = json_encode(['foo' => 'bar']);
    yield \Group\Cache\AsyncRedis::hSet('user', 1, $user);
    dump(yield \Group\Cache\AsyncRedis::hGet('user', 1));
    
```

##### 异常处理

```php

    try {
        throw new \Exception("Error Processing Request", 1); 
        //yield throwException(new \Exception("Error Processing Request", 1));
    } catch (\Exception $e) {
        echo  $e->getMessage();
    }

```
