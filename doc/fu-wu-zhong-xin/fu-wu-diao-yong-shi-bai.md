### 服务调用失败

#### KernalEvent::SERVICE\_FAIL事件

> 在框架层，调用servcie时，会抛出KernalEvent::SERVICE\_FAIL事件，你可以监听该事件，做数据上报处理，请以异步方式上报

#### 配置config/lister.php中的事件监听器

#### 示例
```
<?php

namespace src\Web\Listeners;

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
        yield $this->dosomething();

        yield;
    }
}

```

##### 在框架内部事件中，会做捕捉服务调用失败并做出异常切换。

具体逻辑见Group\Listeners\ServiceFailListener






