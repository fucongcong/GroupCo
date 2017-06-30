<?php

namespace Group\Routing;

use ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return object
     */
    public function register()
    {
        $this->app->singleton('route', function () {
            return RouteService::getInstance();
        });
    }

    public function getName()
    {
        return 'route';
    }
}
