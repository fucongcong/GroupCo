### 异步Http客户端

```php
    use AsyncHttp;

    //直接使用域名, get方式
    $http = new AsyncHttp('http://groupco.com');
    //设置2s超时
    $http->setTimeout(2);
    //$http->setCookies(['token' => 'xxxx']);
    $res = (yield $http->get('/'));

    //也可以通过ip:port方式
    $http = new AsyncHttp('http://127.0.0.1:80');
    $http->setHost('groupco.com');
    $res = (yield $http->get('/user', ['id' => 1]));

    //使用https, post方式
    $http = new AsyncHttp('https://groupco.com');
    $res = (yield $http->post('/test', ['postId' => 52]));
```

### 异步Http客户端

```php
    use AsyncHttp;

    //直接使用域名, get方式
    $http = new AsyncHttp('http://groupco.com');
    //设置2s超时
    $http->setTimeout(2);
    //$http->setCookies(['token' => 'xxxx']);
    $res = (yield $http->get('/'));

    //也可以通过ip:port方式
    $http = new AsyncHttp('http://127.0.0.1:80');
    $http->setHost('groupco.com');
    $res = (yield $http->get('/user', ['id' => 1]));

    //使用https, post方式
    $http = new AsyncHttp('https://groupco.com');
    $res = (yield $http->post('/test', ['postId' => 52]));
```

### 





