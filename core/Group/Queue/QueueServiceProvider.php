<?php

namespace Group\Queue;

use ServiceProvider;
use Group\Queue\QueueService;

class QueueServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return object
     */
    public function register()
    {
        $this->app->singleton('queue', function () {

            return new QueueService;
        });
    }

}
