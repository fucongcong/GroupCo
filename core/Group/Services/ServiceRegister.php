<?php

namespace Group\Services;

class ServiceRegister extends \ServiceProvider
{
	/**
     * Register the service provider.
     *
     * @return object
     */
    public function register()
    {
		$this->app->singleton('service', function () {
            return new \Service();
        });
    }

    public function getName()
    {
        return 'service';
    }
}