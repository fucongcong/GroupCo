<?php

namespace Group\Events\Tests;

use Test;
use Group\EventDispatcher\EventDispatcherService;
use Group\Events\Tests\TestSubscriber;

class EventSubscriberTest extends Test
{
    public function unitAddSubscriber()
    {
        $subscriber = new TestSubscriber();
        $eventDispatcher = new EventDispatcherService();

        $eventDispatcher->addSubscriber($subscriber);

        $this->assertTrue($eventDispatcher->hasListeners('test.start'));
        $this->assertTrue($eventDispatcher->hasListeners('test.stop'));
        $this->assertTrue($eventDispatcher->hasListeners('test.doing'));

        yield $eventDispatcher->dispatch('test.start');
        yield $eventDispatcher->dispatch('test.stop');
        yield $eventDispatcher->dispatch('test.doing');

        $eventDispatcher->removeSubscriber($subscriber);
    }

    public function testRemoveSubscriber()
    {
        $subscriber = new TestSubscriber();
        $eventDispatcher = new EventDispatcherService();

        $eventDispatcher->addSubscriber($subscriber);

        $this->assertTrue($eventDispatcher->hasListeners('test.start'));
        $this->assertTrue($eventDispatcher->hasListeners('test.stop'));
        $this->assertTrue($eventDispatcher->hasListeners('test.doing'));

        $eventDispatcher->removeSubscriber($subscriber);

        $this->assertFalse($eventDispatcher->hasListeners('test.start'));
        $this->assertFalse($eventDispatcher->hasListeners('test.stop'));
        $this->assertFalse($eventDispatcher->hasListeners('test.doing'));
    }
}
