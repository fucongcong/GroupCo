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
//返回User模块下UserDao接口实例
public function getUserDao()
{
    return $this->createDao("User:User");
}

//返回User模块下UserProfileService接口实例
public function getUserProfileService()
{
    return $this->createService("User:UserProfile");
}
```

#### 异步多task任务

**在Service内部封装了一套异步多task模拟map-reduce的处理慢速任务的方法，可以极大提升单个接口吞吐**

```
    //单进程慢速任务 通过异步的多task去做，速度会翻倍
    public function getUsersCache($ids)
    {   
        //异步多task模式。耗时8ms
        foreach ($ids as $id) {
            $this->task('User\User::getUser', ['id' => $id]);
        }

        return $this->finish();

        //正常模式 耗时250ms
        // $users = [];
        // foreach ($ids as $id) {
        //     $users[] = $this->getUser($id);
        // }

        // return $users;
    }
```

**通过**

**$this-&gt;task\($cmd, $data\),**

**$this-&gt;finish\(\);**

**两个方法实现**

* **注意使用此方式实现的接口无法再内部调用。以下方式调用是无效的：**

```
public function getUser($id)
{
     $user = $this->getUserDao()->getUser($id);
     
     //此时无法返回正常数据。
     $user['users'] = $this->getUsersCache([1,2,3]);
     
     return $user;
}

//单进程慢速任务 通过异步的多task去做，速度会翻倍
public function getUsersCache($ids)
{
     //异步多task模式。耗时8ms
     foreach ($ids as $id) {
     $this->task('User\User::getUser', ['id' => $id]);
     }
     
     return $this->finish();

}
```



