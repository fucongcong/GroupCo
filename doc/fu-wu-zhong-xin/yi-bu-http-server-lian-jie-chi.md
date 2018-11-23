### 在异步HTTP SERVER中使用TCP连接池调用服务

#### 全局方法service\_center\(\)

使用service\_center\($service, $enablePool = false\)获取服务地址,然后使用call\(\)方法调用公开的服务方法

设置第二个参数为true时，将开启TCP连接池。连接池数量可在config/app.php中配置参数maxPool，默认为10

```
    $service = (yield service_center('User', true));
    $user = (yield $service->call("User::getUser", ['id' => $userId]));
```


#### 同理，全局方法service\(\)

使用service\($serverName, $enablePool = false\)获取服务地址,然后使用call\(\)方法调用公开的服务方法.

service\(\)不会通过注册中心发现服务

设置第二个参数为true时，将开启TCP连接池。连接池数量可在config/app.php中配置参数maxPool，默认为10

```
    service("user", true)->setTimeout(2);
    $users = (yield service("user")->call("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5]]));
```