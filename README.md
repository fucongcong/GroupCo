# Group-Co	

#### 异步协程，应对高并发

#### SOA服务化调用，支持并行、串行调用

#### 支持异步日志,异步Mysql,异步Redis

#### 支持数据库连接池

#### 强大的Twig、Doctrine支持视图、数据层

联系我Email: cc@xitongxue.com,coco.fu@clothesmake.com

##### 示例代码
```php
<?php

namespace src\Web\Controller\Demo;

use Controller;
use Request;

class DemoController extends Controller
{
    public function addAction()
    {
        yield new \Response('1');
    }

    public function testAction(Request $request, $id)
    {  
        //串行
        $start = microtime(true);
        //设置2秒超时
        $this->getUserService()->setTimeout(2);
        $users = (yield $this->getUserService()->call("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]));
        dump($users);

        $users = (yield $this->getUserService()->call("User\User::getUser", ['id' => 1]));
        dump($users);
        dump(microtime(true) - $start);

        //并行
        $start = microtime(true);
        $callId1 = $this->getUserService()->addCall("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]);
        $callId2 = $this->getUserService()->addCall("User\User::getUser", ['id' => 1]);
        $res = (yield $this->getUserService()->multiCall());
        dump($res[$callId1]);
        dump($res[$callId2]);
        dump(microtime(true) - $start);

        //异步redis
        yield \Group\Cache\AsyncRedis::set('foo', 'bar');
        dump(yield \Group\Cache\AsyncRedis::get('foo'));
        $user = json_encode(['foo' => 'bar']);
        yield \Group\Cache\AsyncRedis::hSet('user', 1, $user);
        dump(yield \Group\Cache\AsyncRedis::hGet('user', 1));

        //异常处理
        try {
            yield $this->testException();
            //yield throwException(new \Exception("Error Processing Request", 1));
        } catch (\Exception $e) {
            echo  $e->getMessage();
        }

        yield $this->render('Web/Views/Group/index.html.twig', [
            'user' => $users
            ]);
    }

    public function getUserService()
    {
        return service("user_service");
    }

    public function testException()
    {
        throw new \Exception("Error Processing Request", 1); 
    }
}


```