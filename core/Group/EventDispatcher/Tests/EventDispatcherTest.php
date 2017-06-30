<?php

namespace Group\Session\Tests;

use Test;
use Group\EventDispatcher\EventDispatcher;
use Listener;
use Group\Listeners\KernalResponseListener;
use Group\Events\HttpEvent;
use Response;

class EventDispatcherTest extends Test
{
    public function testaddListener()
    {
        $listener = new KernalResponseListener();
        EventDispatcher::addListener('kernal.responese', $listener, 10);

        $this->assertTrue(EventDispatcher::hasListeners('kernal.responese'));

        EventDispatcher::removeListener('kernal.responese', $listener);
        $this->assertFalse(EventDispatcher::hasListeners('kernal.responese'));

        EventDispatcher::addListener('kernal.request', function($event){
            //do something
        }, 100);
    }

    public function testgetListener()
    {
        $listener = new KernalResponseListener();
        EventDispatcher::addListener('kernal.responese', $listener, 10);

        $object = EventDispatcher::getListeners('kernal.responese');
        $this->assertTrue($object[0] instanceof KernalResponseListener);
    }

    public function testdispatch()
    {
        $event = new HttpEvent(null, new Response());
        EventDispatcher::dispatch('kernal.responese', $event);
    }
}
