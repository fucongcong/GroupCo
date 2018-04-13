### 服务异常邮件通知

#### 这里使用swiftMail作为邮件类

    composer require swiftmailer/swiftmailer

#### 在config配置文件下新建param.php,配置邮件相关信息

```php

    <?php

    return [
        #swift mail
        'swift.mail.host' => 'smtp.exmail.qq.com',
        'swift.mail.port' => 25,
        'swift.mail.username' => 'xxx@xxx.com',
        'swift.mail.password' => 'xxx',
        'swift.mail.to' => ['xxx@xxx.com', 'aa@aaa.com'],
    ];


```

#### 编写监听KernalEvent::SERVICE_FAIL事件

在config/dev/listener.php中加入如下配置


```php

    <?php

    return [
        'services' => [
            [
                'eventName' => 'kernal.service_fail',
                'listener'  => 'src\Common\Listeners\ServiceFailListener',
                'priority'  => 10,
            ],
        ]
    ];

```

#### 编写src\Common\Listeners\ServiceFailListener类

> 我们使用redis任务队列进行异常服务的统计，并启一个子进程进行消费。


``` php
<?php

namespace src\Common\Listeners;

use AsyncRedis;

class ServiceFailListener extends \Listener
{
    public function setMethod()
    {
        return 'onServiceFail';
    }

    /**
     * 服务调用失败事件
     * @param  \Event
     */
    public function onServiceFail(\Event $event)
    {
        //当服务调用失败时，你可以做上报监控平台，邮件通知等等业务。请以异步方式上报
        $data = $event->getProperty();
        //无可用的服务
        yield AsyncRedis::rPush('service_fail', json_encode([
            'service' => $data['service'],
            'cmd' => $data['cmd'],
            'ip' => $data['ip'],
            'port' => $data['port'],
        ]));
        yield;
    }
}

```

#### 配置config/dev/app.php中的process选项

```php

    'process' => [
        'src\Common\Process\ServiceFailProcess',
    ],

```
#### 实现src\Common\Process\ServiceFailProcess消费队列，发送邮件

```php
<?php

namespace src\Common\Process;

use Group\Process;
use Redis;
use swoole_process;
use Group\Config\Config;

class ServiceFailProcess extends Process
{   
    public function register()
    {   
        $config = Config::get("database::redis");
        $config = $config['default'];

        $redis = new Redis;
        $redis->connect($config['host'], $config['port']);
        if (isset($config['auth'])) {
            $redis->auth($config['auth']);
        }
        $redis->setOption(Redis::OPT_PREFIX, isset($config['prefix']) ? $config['prefix'] : '');

        $process = new swoole_process(function($process) use ($redis) {
            //轮询
            swoole_timer_tick(1000, function() use ($redis) {
                $errors = $redis->lPop('service_fail');
                if ($errors) {
                    $errors = json_decode($errors, true);
                    //发送邮件

                    if (is_array($errors['cmd'])) {
                        $cmd = implode(",", $errors['cmd']);
                    } else {
                        $cmd = $errors['cmd'];
                    }

                    $email = new Email;
                    if ($errors['ip'] == "" || $errors['port'] == "") {

                        $email->sendEmail("【服务调用失败】".$errors['service']."服务", "没有一个可用的服务。调用命令:".$cmd);
                    } else {
                        $email->sendEmail("【服务调用失败】".$errors['service']."服务", "服务地址: ".$errors['ip'].":".$errors['port']."。调用命令:".$cmd);
                    }
                    unset($email);
                }
            });
        });

        return $process;
    }
}


class Email
{
    public function sendEmail($title, $body, $format = 'text')
    {   
        $to = Config::get("param::swift.mail.to");
        $host = Config::get("param::swift.mail.host");
        $port = Config::get("param::swift.mail.port");
        $username = Config::get("param::swift.mail.username");
        $password = Config::get("param::swift.mail.password");

        if ($format == "html") {
            $format = 'text/html';
        } else {
            $format = 'text/plain';
        }

        $transport = \Swift_SmtpTransport::newInstance($host, $port)
            ->setUsername($username)
            ->setPassword($password);

        $mailer = \Swift_Mailer::newInstance($transport);

        $email = \Swift_Message::newInstance();
        $email->setSubject($title);
        $email->setFrom(array($username => 'clothesmake'));
        $email->setTo($to);
        if ($format == 'text/html') {
            $email->setBody($body, 'text/html');
        } else {
            $email->setBody($body);
        }

        $res = $mailer->send($email);

        if ($res == 1) {
            return true;
        }

        return false;
    }
}

```

#### 重启主服务即可~