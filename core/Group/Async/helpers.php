<?php

use Group\Async\AsyncApp;

if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param  string  $abstract
     * @return mixed|\Group\App\App
     */
    function app($abstract = null)
    {   
        if (is_null($abstract)) {
            return AsyncApp::getInstance();
        }

        if (ucfirst($abstract) == 'Container') {
            return Container::getInstance();
        }

        return AsyncApp::getInstance()->make($abstract);
    }
}