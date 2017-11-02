#### 使用注册中心

##### 设置注册中心
修改config/service.php中的registry_address.目前只支持redis注册中心 

    'registry_address' => 'redis://127.0.0.1:6379'

##### 设置依赖的服务
修改config/app.php的services.你需要将项目依赖的服务模块写入该数组，在server启动时，会单独起一个进程订阅每个服务

    //依赖的服务模块 
    'services' => ["User", "Order", "Monitor"],

##### 启动服务
比如启动User服务。

    app/service user

##### 在异步server中使用服务
使用service_center($service)获取服务地址,然后使用call()方法调用公开的服务方法

    $service = (yield service_center('User'));
    $user = (yield $service->call("User::getUser", ['id' => $userId]));

##### 使用monitor服务，监控其他服务
monitor服务会单独起一个进程，定时轮询已上线的服务的健康度。从注册中心摘除异常服务

    app/service monitor

##### 注册中心还有许多优化点，包括后期会支持基于数据库、zookeeper等等的注册中心。