### 在异步HTTP SERVER中使用服务

#### 全局方法service\_center\(\)

使用service\_center\($service\)获取服务地址,然后使用call\(\)方法调用公开的服务方法

```
    $service = (yield service_center('User'));
    $user = (yield $service->call("User::getUser", ['id' => $userId]));
```

> 上面的代码会调用src/Service/User/Service/Impl/UserServiceImpl中的getUser方法

#### 串行调用

```
    //设置2秒超时
    $service = (yield service_center("User"));
    $service->setTimeout(2);
    $users = (yield $service->call("User::getUsersCache", ['ids' => [1, 2]]));
    $users2 = (yield $service->call("User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]));
```

#### 并行调用（只能针对同一服务模块）

```
    $service = (yield service_center("User"));
    $service->setTimeout(2);

    $callId1 = $service->addCall("User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]);
    $callId2 = $service->addCall("User::getUser", ['id' => 1]);
    $res = (yield $service->multiCall());

    dump($res[$callId1]);
    dump($res[$callId2]);
```

#### 全局方法service\(\)

使用service\($serverName\)获取服务地址,然后使用call\(\)方法调用公开的服务方法.

service\(\)不会通过注册中心发现服务

```
    service("user")->setTimeout(2);
    $users = (yield service("user")->call("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5]]));
```

#### 串行调用

```
    service("user")->setTimeout(2);
    $users = (yield service("user")->call("User\User::getUsersCache", ['ids' => [1, 2, 3, 4]]));
    $users2 = (yield service("user")->call("User\User::getUsersCache", ['ids' => [1, 2, 3, 4]]));
```

#### 并行调用

```
    service("user")->setTimeout(2);

    $callId1 = service("user")->addCall("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5]]);
    $callId2 = service("user")->addCall("User\User::getUser", ['id' => 1]);
    $res = (yield service("user")->multiCall());

    dump($res[$callId1]);
    dump($res[$callId2]);
```



