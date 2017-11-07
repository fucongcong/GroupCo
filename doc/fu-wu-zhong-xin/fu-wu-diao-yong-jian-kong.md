### 服务调用监控

#### KernalEvent::SERVICE\_CALL事件

> 在框架层，调用servcie时，会抛出KernalEvent::SERVICE\_CALL事件，你可以监听该事件，做数据上报处理，请以异步方式上报

```
    <?php

    namespace src\Web\Listeners;

    use Listener;
    use Event;

    class ServiceCallListener extends Listener
    {
        public function setMethod()
        {
            return 'onServiceCall';
        }

        public function onServiceCall(Event $event)
        {
            $data = $event->getProperty();
            $cmd = $data['cmd'];
            $calltime = $data['calltime'];

            //上报监控平台
            //do something
        }
    }
```

##### 具体可见Event基础服务使用



