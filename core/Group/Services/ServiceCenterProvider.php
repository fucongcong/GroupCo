<?php

namespace Group\Services;

class ServiceCenterProvider extends \ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return object
     */
    public function register()
    {
        $this->app->singleton('serviceCenter', function () {
            return new \ServiceCenter();
        });
    }

    public function getName()
    {
        return 'service_center';
    }
}