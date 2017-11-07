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



