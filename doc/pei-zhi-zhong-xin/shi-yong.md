### 使用配置中心

#### 使用场景
当服务器需要动态更新配置时，可以开启使用配置中心。比如修改配置中的某个api地址时,需要即使生效。

#### 确保框架内核版本 >=2.0.1,可查看composer.json
    
    "group-co/framework": "^2.0.1"

#### 目前实现了以携程开源的apollo为服务端的配置中心
- [APOLLO](https://github.com/ctripcorp/apollo)

#### 开启配置中心,修改app.php 新增如下配置 

```php
    'config_center' => "apollo",
    'config_url' => "http://localhost:8080",
    'file_type' => "yml", //namespace 的文件格式类型，我们只支持 json,yaml,yml
    'appId' => 'co', //当前的appID
    'cluster' => 'default', //集群配置
    'poll_time' => 2, //轮询周期，建议30-60S左右
```
##### 注意事项
- 在apollo后台创建namespace时候，支持的配置文件格式目前为json,yaml,yml。

#### 使用demo
- 首先在apollo后台新增一个名为param的私有namespace,以yml为存储格式
- 设置内容为 env: prod

使用方式与Config配置类使用一模一样。
```php
    echo \Config::get('param:env', 'dev');
    //结果为prod
```

