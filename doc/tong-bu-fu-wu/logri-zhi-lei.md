### Log

#### 默认路径存放于runtime/logs/service

```php

    Log::debug('123',['user'=>1]);
    Log::info('123',['user'=>1]);
    Log::notice('123',['user'=>1]);
    Log::warning('123',['user'=>1]);
    Log::error('123',['user'=>1]);
    Log::critical('123',['user'=>1]);
    Log::alert('123',['user'=>1]);
    Log::emergency('123',['user'=>1]);

    //默认model是web.app,也可以自定义
    Log::emergency('123',['user'=>1],'web.admin');
```

## 





