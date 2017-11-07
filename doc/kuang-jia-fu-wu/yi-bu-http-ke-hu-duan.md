### 异步Http客户端

#### Get方式

1.使用域名形式

```php
    use AsyncHttp;

    //直接使用域名, get方式
    $http = new AsyncHttp('http://groupco.com');
    //设置2s超时
    $http->setTimeout(2);
    //$http->setCookies(['token' => 'xxxx']);
    $res = (yield $http->get('/'));
```

2.使用ip:port形式

```php
    use AsyncHttp;

    //也可以通过ip:port方式
    $http = new AsyncHttp('http://127.0.0.1:80');
    $http->setHost('groupco.com');
    $res = (yield $http->get('/user', ['id' => 1]));
```

#### Post方式

1.使用域名形式

```php
    use AsyncHttp;

    //使用https, post方式
    $http = new AsyncHttp('https://groupco.com');
    $res = (yield $http->post('/test', ['postId' => 52]));
```

2.使用ip:port形式

```php
    use AsyncHttp;

    //也可以通过ip:port方式
    $http = new AsyncHttp('http://127.0.0.1:80');
    $http->setHost('groupco.com');
    $res = (yield $http->post('/test', ['postId' => 52]));
```

> ##### 注：若请求https地址，需要在编译swoole时开启openssl



