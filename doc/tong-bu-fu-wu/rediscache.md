### Cache

##### 目前只支持了Redis得cache，使用请在config/database.php配置中配置'cache' =&gt; 'redis',

```php
    use Cache;
    //key value expireTime
    Cache::set('ha', 123, 60);
    //也可以这样
    Cache::redis() -> set('haa', 123, 60);

    Cache::get('ha');
    Cache::mGet(['ha', 'aa']);
    Cache::hSet($hashKey, $key, $data, $expireTime);
    Cache::hGet($hashKey, $key);
    Cache::hDel($hashKey, $key);
    Cache::hDel($hashKey);

    //现在的类库方法还未扩展完全，目前只有以上方法
```

* ##### 你可以使用Cache::redis\(\) 获取redis实例，这是一个PhpRedis的实例，api地址\([https://github.com/phpredis/phpredis\](https://github.com/phpredis/phpredis\)\)





