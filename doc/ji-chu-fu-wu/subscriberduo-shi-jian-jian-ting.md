### Subscriber多事件监听

```
<?php

namespace Group\Events\Tests;

use Group\Events\EventSubscriberInterface;

class TestSubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents()
    {
        return [

            //eventName  =>  listener
            'test.start' => 'onTestStart',
            //eventName  =>  listener, priority
            'test.stop' => ['onTestStop', 100],
            //eventName  => array  listener, priority
            'test.doing' => [
                ['onDoA'],
                ['onDoB', 225],
            ],
        ];
    }

    public function onTestStart(\Event $event)
    {
        echo 'onTestStart';
    }

    public function onTestStop(\Event $event)
    {
        echo 'onTestStop';
    }

    public function onDoA(\Event $event)
    {
        echo 'onDoA';
    }

    public function onDoB(\Event $event)
    {
        echo 'onDoB';
    }
}
```

#### 如何绑定多事件监听Subscriber见EventDispatcher事件调度



