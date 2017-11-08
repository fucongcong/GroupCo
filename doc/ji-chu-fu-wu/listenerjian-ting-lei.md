### Listener

#### 原型

```
<?php

namespace Group\Listeners;

abstract class Listener
{
    abstract function setMethod();

    public function getMethod()
    {
        return $this->setMethod();
    }
}
```

#### 实现一个监听类

```
<?php

namespace src\Web\Listeners;

use Listener;
use Event;

class KernalResponseListener extends Listener
{   
    //设置执行的方法
    public function setMethod()
    {
        return 'onKernalResponse';
    }

    //触发时执行
    public function onKernalResponse(Event $event)
    {
        echo 'this is a KernalResponse Listener';
    }
}
```

#### 如何绑定事件监听Listener见EventDispatcher事件调度



