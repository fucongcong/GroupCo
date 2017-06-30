<?php

namespace Group\Contracts\EventDispatcher;

use Group\Events\EventSubscriberInterface;
use Event;

interface EventDispatcher
{
    /**
     * dispatch the event
     *
     * @param  eventName
     * @param  Event event
     * @return Event event
     */
    public function dispatch($eventName, Event $event = null);

    /**
     * add listener for event
     *
     * @param  eventName
     * @param  listener  可以是一个callback方法或者一个继承于Listner的对象实例
     * @param  priority  越大越早执行
     */
    public function addListener($eventName, $listener, $priority = 0);

    /**
     * remove listener
     *
     * @param  eventName
     * @param  listener  可以是一个callback方法或者一个继承于Listner的对象实例
     */
    public function removeListener($eventName, $listener);

    /**
     * 获取排好序的listeners
     *
     * @param  eventName
     * @return  array
     */
    public function getListeners($eventName = null);

    /**
     * 是否存在某个监听
     *
     * @param  eventName
     * @return  boolean
     */
    public function hasListeners($eventName = null);

    /**
     * 移除绑定器
     *
     * @param  EventSubscriberInterface subscriber
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber);

    /**
     * 添加事件绑定器，可以绑定多个事件
     *
     * @param  EventSubscriberInterface subscriber
     */
    public function addSubscriber(EventSubscriberInterface $subscriber);
}
