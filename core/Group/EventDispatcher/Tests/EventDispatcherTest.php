<?php

namespace Group\EventDispatcher\Tests;

use Test;
use Group\EventDispatcher\EventDispatcherService;
use Listener;
use Group\Listeners\KernalResponseListener;
use Group\Events\HttpEvent;
use Response;

class EventDispatcherTest extends Test
{
    public function testaddListener()
    {
        $listener = new KernalResponseListener();
        $eventDispatcher = new EventDispatcherService();

        $eventDispatcher->addListener('kernal.responese', $listener, 10);

        $this->assertTrue($eventDispatcher->hasListeners('kernal.responese'));

        $eventDispatcher->removeListener('kernal.responese', $listener);
        $this->assertFalse($eventDispatcher->hasListeners('kernal.responese'));

        $eventDispatcher->addListener('kernal.request', function($event){
            //do something
        }, 100);
    }

    public function testgetListener()
    {
        $listener = new KernalResponseListener();
        $eventDispatcher = new EventDispatcherService();

        $eventDispatcher->addListener('kernal.responese', $listener, 10);

        $object = $eventDispatcher->getListeners('kernal.responese');
        $this->assertTrue($object[0] instanceof KernalResponseListener);
    }
}
