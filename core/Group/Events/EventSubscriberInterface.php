<?php

namespace Group\Events;

/**
 * 这是一个时间绑定器的接口
 *
 */
interface EventSubscriberInterface
{
    /**
     * 定义绑定事件
     *
     * @return array 可以看Tests/TestSubscriber.php
     */
    public function getSubscribedEvents();
}
