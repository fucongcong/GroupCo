<?php

namespace Group\EventDispatcher;

use ServiceProvider;
use Group\EventDispatcher\EventDispatcherService;
use Group\Listeners\KernalInitListener;
use Group\Events\KernalEvent;

class EventDispatcherServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return object
     */
    public function register()
    {
        $this->app->singleton('eventDispatcher', function () {
            $eventDispatcher = new EventDispatcherService();
            $eventDispatcher->addListener(KernalEvent::INIT, new KernalInitListener());
            return $eventDispatcher;
        });

        // $eventDispatcher = new EventDispatcherService();
        // $eventDispatcher->addListener(KernalEvent::INIT, new KernalInitListener());

        // yield $this->app->setTaskSingleton('eventDispatcher', $eventDispatcher);
    }

    public function getName()
    {
        return 'eventDispatcher';
    }
}
