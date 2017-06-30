<?php

namespace Group\Services;

abstract class ServiceProvider
{
    public $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 注册service
     *
     * @return Service
     */
    abstract public function register();

    abstract public function getName();
}
