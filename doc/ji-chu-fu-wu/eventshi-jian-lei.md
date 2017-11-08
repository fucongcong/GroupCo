### Event事件

#### 自定义事件

* ##### 事件对象Event,自定义Event {#事件对象event与如何定义一个event，例如：}

```
<?php

namespace Group\Events;

class Event
{   
    protected $property;

    public function __construct($property = null)
    {
        $this->property = $property;
    }

    public function getProperty()
    {
        return $this->property;
    }

    public function setProperty($property)
    {
        $this->property = $property;
    }
}
```

* ##### 自定义Event {#事件对象event与如何定义一个event，例如：}

```
<?php

namespace Group\Events;

final class QueueEvent extends \Event
{   
    const CRASH = "server.crash";

    protected $server;

    protected $host;

    public function __construct($server, $host)
    {   
        $this->server = $server;
        $this->host = $host;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function getHost()
    {
        return $this->host;
    }
}
```

#### 框架内部事件

* ##### kernal.init {#kernalinit}
* ##### kernal.response {#kernalresponse}
* ##### kernal.request {#kernalrequest}
* ##### kernal.exception {#kernalexception}
* ##### kernal.notfound {#kernalnotfound}
* ##### kernal.httpfinish {#kernalhttpfinish}
* ##### kernal.service\_call {#kernalhttpfinish}
* ##### kernal.service\_fail {#kernalhttpfinish}

#### 预先需要绑定的监听事件,可以编辑config/listener.php,添加预绑定事件的监听器

#### 如何派发事件Event见EventDispatcher事件调度



