<?php

namespace Group\EventDispatcher;

use Group\Contracts\EventDispatcher\EventDispatcher as EventDispatcherContract;
use Group\Events\EventSubscriberInterface;
use Listener;
use Event;

class EventDispatcherService implements EventDispatcherContract
{
    /**
     *
     * @var listeners
     */
    protected $listeners = [];

    /**
     *
     * @var sorted listeners
     */
    protected $sorted = [];

    /**
     * dispatch the event
     *
     * @param  eventName
     * @param  Event event 可以是一个继承自Event得实例
     * @return Event event
     */
    public function dispatch($eventName, Event $event = null)
    {
        if (empty($event)) $event = new Event;

        $this->setEvents($eventName);

        if (isset($this->listeners[$eventName])) {
            return $this->doDispatch($eventName, $event);
        }

        return $event;
    }

    /**
     * add listener for event
     *
     * @param  eventName
     * @param  listener  可以是一个callback方法或者一个继承于Listner的对象实例
     * @param  priority  越大越早执行
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->listeners[$eventName][$priority][] = $listener;
        $this->sortEvents($eventName);
    }

    /**
     * remove listener
     *
     * @param  eventName
     * @param  listener  可以是一个callback方法或者一个继承于Listner的对象实例
     */
    public function removeListener($eventName, $listener)
    {
       if (!isset($this->listeners[$eventName])) {
            return;
        }

        foreach ($this->listeners[$eventName] as $priority => $listeners) {
            if (false !== ($key = array_search($listener, $listeners, true))) {
                unset($this->listeners[$eventName][$priority][$key]);
                $this->sortEvents($eventName);
            }
        }
    }

    /**
     * 对eventName的listeners排序
     *
     * @param  eventName
     */
    private function sortEvents($eventName)
    {
        if (isset($this->listeners[$eventName])) {
            krsort($this->listeners[$eventName]);
            $this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
        }
    }

    /**
     * 设置一个有序的sorted
     *
     * @param  eventName
     */
    private function setEvents($eventName)
    {
        if (!isset($this->sorted[$eventName]))
            $this->sorted[$eventName] = [];
    }

    /**
     * 获取排好序的listeners
     *
     * @param  eventName
     * @return  array|null
     */
    public function getListeners($eventName = null)
    {
        if (isset($eventName)) {
            return isset($this->sorted[$eventName]) ? $this->sorted[$eventName] : null;
        }

        return $this->sorted;
    }

    /**
     * 是否存在某个监听
     *
     * @param  eventName
     * @return  boolean
     */
    public function hasListeners($eventName = null)
    {
        if (isset($eventName) && isset($this->sorted[$eventName])) {
            return  empty($this->sorted[$eventName]) ? false : true;
        }

        return empty($this->sorted) ? false : true;
    }

    /**
     * 添加事件绑定器，可以绑定多个事件
     *
     * @param  EventSubscriberInterface subscriber
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_string($params)) {
                $this->addListener($eventName, array($subscriber, $params));

            } elseif (is_string($params[0])) {
                $this->addListener($eventName, array($subscriber, $params[0]), isset($params[1]) ? $params[1] : 0);

            } else {
                foreach ($params as $listener) {
                    $this->addListener($eventName, array($subscriber, $listener[0]), isset($listener[1]) ? $listener[1] : 0);
                }
            }
        }
    }

    /**
     * 移除绑定器
     *
     * @param  EventSubscriberInterface subscriber
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_array($params) && is_array($params[0])) {
                foreach ($params as $listener) {
                    $this->removeListener($eventName, array($subscriber, $listener[0]));
                }
            } else {
                $this->removeListener($eventName, array($subscriber, is_string($params) ? $params : $params[0]));
            }
        }
    }

    /**
     * 执行触发事件
     *
     * @param  eventName
     * @param  Event event
     */
    private function doDispatch($eventName, $event)
    {
        $listeners = $this->sorted[$eventName];

        foreach ($listeners as $listener) {
            if (is_callable($listener, false)) {
                //$listener($event);
                call_user_func($listener, $event);
            }
            if ($listener instanceof Listener) {
                // $method = $listener->getMethod();
                // $listener->$method($event);
                call_user_func_array([$listener, $listener->getMethod()], [$event]);
            }
        }
    }
}
