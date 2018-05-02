### Cache

#### redis-cluster集群配置. 修改congif/{env}/database.php中的redis选项
- 将cluster设为true，表示使用集群模式
- 设置option参数
- 配置集群服务列表即可
- 重启服务即可，此时redis已切换为集群模式
```php
    'redis' => [
        //集群模式只在service下使用有效，异步redis中并不适用
        'cluster' => true,
        'cluster_options' => [
            'connect_timeout' => 2,
            'read_timeout' => 2,
            'connect'  => 'persistence',
            'prefix' => 'groupa:',
        ],
        'clusters' => [
            'default' => [
                'host' => '127.0.0.1',
                'port'     => 6382,
            ],
            'redis1' => [
                'host' => '127.0.0.1',
                'port'     => 6380,
            ],
            'redis2' => [
                'host' => '127.0.0.1',
                'port'     => 6381,
            ],
        ],
    ],
```

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





