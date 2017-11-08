### EventDispatcher事件调度

#### 派发事件 {#2自定义事件}

##### EventDispatcher::dispatch\($eventName, Event $event = null\)

```
use EventDispatcher;

EventDispatcher::dispatch('user.login');

//你也可以在抛出事件时，塞入事件对象
EventDispatcher::dispatch('user.register', new \Event($user));

EventDispatcher::dispatch('user.register', new \UserEvent($user));
```

#### 绑定监听事件

##### EventDispatcher::addListener\($eventName, $listener, $priority = 0\)

> \* @param  eventName
>
> ```
>  \* @param  listener  可以是一个callback方法或者一个继承于Listner的对象实例
>
>  \* @param  priority  越大越早执行
> ```

##### EventDispatcher::removeListener\($eventName, $listener\)

> \* @param  eventName
>
> ```
>  \* @param  listener  可以是一个callback方法或者一个继承于Listner的对象实例
> ```

##### EventDispatcher::hasListeners\($eventName = null\)

##### EventDispatcher::getListeners\($eventName = null\)

##### 例子：

```
use EventDispatcher;

$listener = new KernalResponseListener();
//定义一个事件名称，触发的监听器，和一个重要指数
EventDispatcher::addListener('kernal.responese', $listener, 10);

EventDispatcher::removeListener('kernal.responese', $listener);

EventDispatcher::hasListeners('kernal.responese');
```

#### 绑定多事件监听类

##### EventDispatcher::addSubscriber\(EventSubscriberInterface $subscriber\)

##### EventDispatcher::removeSubscriber\(EventSubscriberInterface $subscriber\)

```
$subscriber = new TestSubscriber();

EventDispatcher::addSubscriber($subscriber);

EventDispatcher::hasListeners('test.start')
EventDispatcher::hasListeners('test.stop')
EventDispatcher::hasListeners('test.doing')

EventDispatcher::dispatch('test.start');
EventDispatcher::dispatch('test.stop');
EventDispatcher::dispatch('test.doing');

EventDispatcher::removeSubscriber($subscriber);
```



