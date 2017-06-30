<?php

namespace Group\Listeners;

use Group\Events\KernalEvent;
use Group\Exceptions\NotFoundException;

class KernalInitListener extends \Listener
{
    public function setMethod()
    {
        return 'onKernalInit';
    }

    public function onKernalInit(\Event $event)
    {
        $listeners = [
            [
                'eventName' => KernalEvent::REQUEST,
                'listener'  => 'Group\Listeners\KernalRequestListener',
                'priority'  => 0,
            ],
            [
                'eventName' => KernalEvent::RESPONSE,
                'listener'  => 'Group\Listeners\KernalResponseListener',
                'priority'  => 0,
            ],
            [
                'eventName' => KernalEvent::EXCEPTION,
                'listener'  => 'Group\Listeners\ExceptionListener',
                'priority'  => 0,
            ],
            [
                'eventName' => KernalEvent::NOTFOUND,
                'listener'  => 'Group\Listeners\NotFoundListener',
                'priority'  => 0,
            ],
        ];

        $listeners = array_merge(\Config::get('listener::services'), $listeners);

        foreach ($listeners as $listener) {
            if (!class_exists($listener['listener'])) {
                throw new NotFoundException("Class ".$listener['listener']." not found !");
            }

            $lis = new $listener['listener'];
            if (!$lis instanceof Listener) {
                throw new \RuntimeException("Class ".$listener['listener']." must instanceof Listener !");
            }

            $container = $event->getProperty();
            $container->singleton('eventDispatcher')->addListener($listener['eventName'], $lis, $listener['priority']);
        }
    }
}
