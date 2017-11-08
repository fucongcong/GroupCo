### Service服务

* > #### Service服务是配合服务中心来实现服务化的。每个服务可以单独启动一个server，处理请求。
  >
  > #### 在开发Service模块时候，同样要注意内存释放问题。并且不可以使用异步服务，此模块是以同步方式执行的。
  >
  > #### 所以说，你可以用传统的方式来编写服务接口，当然你也可以使用内置的一些异步task方法来实现map-reduce，提升接口吞吐。

#### 1.开启服务如执行 app/service user，会开放服务下面指定开放模块所有公有函数调用

#### 2.使用Console控制台，自动初始化服务

```
app/console generate:service demo
```

#### 3.简单介绍一下生成的服务目录结构

* User \(示例\)

  * Dao

    * Impl \(数据层实现的接口\)

      * UserDaoImpl.php\(接口实现\)

    * UserDao.php\(接口\)

  * Service

    * Impl （服务层实现的接口）

      * UserServiceImpl.php\(接口的实现\)

    * Rely （定义服务之间的依赖关系）

    * UserService.php\(接口\)

#### Service类

$this-&gt;createDao\($serviceName\)

> 实例化一个dao类

$this-&gt;createService\($serviceName\)

> 实例化一个service类

```
public function getUserDao()
{
    return $this->createDao("User:User");
}


public function getUserProfileService()
{
    return $this->createService("User:UserProfile");
}
```



