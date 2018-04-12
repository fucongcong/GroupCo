### 单元测试

- PHPUNIT本身是不支持异步框架的测试的，我们在使用PHPUNIT时，做了一些改变。
- PHPUNIT中的注解无法生效
- 所有测试方法以unitDemo(),以unit开头。区别于test开头

#### 测试建议，我们建议对聚合层与服务层统一做单元测试的编写。（数据库环境的配置，建议在config下面新增test配置文件，来跑单元测试）

#### 聚合层测试示例

```php
    <?php

    namespace src\Shop\Tests\Api;

    use Test;
    use AsyncHttp;

    class AuthTest extends Test
    {
        public function unitLoginAction()
        {
            $http = new AsyncHttp('http://127.0.0.1:9777');

            $res = (yield $http->post('/api/shop/login', ['account' => '00000001', 'password' => '2skn2w']));
            $data = json_decode($res->body, true);
            $this->assertEquals(1011, $data['code']);

            $res = (yield $http->post('/api/shop/login', ['account' => '00000001', 'password' => '1']));
            $data = json_decode($res->body, true);
            $this->assertEquals(1003, $data['code']);

            $res = (yield $http->post('/api/shop/login', ['account' => '18768176260', 'password' => '11111']));
            $data = json_decode($res->body, true);
            $this->assertEquals(200, $data['code']);
        }
    }

```

#### service测试服务的配置

    //用于测试
    'test' => [
        //本机当前内网ip
        'ip' => '127.0.0.1',
        'serv' => '0.0.0.0',
        'port' => 9511,
        'config' => [
            'daemonize' => true,        
            'worker_num' => 2,
            'max_request' => 50000,
            'task_worker_num' => 5,
            'task_max_request' => 50000,
            'heartbeat_idle_time' => 300,
            'heartbeat_check_interval' => 60,
            'dispatch_mode' => 3,
            'log_file' => 'runtime/service/test/test.log',
        ]
    ],

test环境不设置public的属性，将公开所有服务供调用


#### 服务层测试

```php
    <?php

    namespace src\Service\Sms\Tests;

    use Test;

    class SmsServiceTest extends Test
    {
        public function unitSendSms()
        {
            $res = (yield service('test')->call("Sms\Sms::sendSms", ['mobile' => '18768176260']));
            $this->assertEquals(['code' => 200], $res);
        }

        public function unitIsActiveCode()
        {
            $res = (yield service('test')->call("Sms\Sms::isActiveCode", ['mobile' => '18768176260', 'code' => 1234]));
            $this->assertTrue($res);
        }
    }

```

#### 单独测试服务层流程
- 启动test服务 app/service test
- 开始测试 phpunit --bootstrap app/test.php src/Service

#### 测试聚合层与服务层流程
- 启动test服务 app/service test
- 启动主服务   php server.php
- 开始测试 phpunit --bootstrap app/test.php src
