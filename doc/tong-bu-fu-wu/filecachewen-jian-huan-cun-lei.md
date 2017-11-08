### FileCache

##### 文件形式的缓存

#### FileCache::get\($cacheName, $cacheDir = false\)

```
use FileCache;

FileCache::get('test.php');
FileCache::get('test.php', 'runtime/cache/test/');
```

#### FileCache::set\($cacheName, $data, $cacheDir = false, $flag = false\)

```
use FileCache;

//默认路径是放在runtime/logs/service
FileCache::set('test.php', ['testdfata' => 'datadata']);
//指定路径
FileCache::set('test.php', ['testdfata' => 'datadata'], 'runtime/cache/test/', FILE_APPEND);
```



