### StaticCache

##### 静态变量形式的缓存\(是存放于当前进程的内存中\)。注册中心的服务地址也存放于静态缓存中。

#### StaticCache::set\($key, $value, $canUnset = true\)

```
    use StaticCache;

    StaticCache::set('foo', 'bar');
    //默认该变量可以被释放，也可设置不释放。使用情况：在使用Async服务中，会涉及静态变量释放的问题。
    StaticCache::set('foo', 'bar', false);
```

#### StaticCache::get\($key, $default = null\)

```
    use StaticCache;

    StaticCache::get('foo'); //return bar
```

#### StaticCache::flush\(\)

```
    use StaticCache;

    //刷新可释放的静态变量
    StaticCache::flush();
```

#### StaticCache::flushAll\(\)

```
    use StaticCache;

    //刷新所有静态变量
    StaticCache::flushAll();
```



