<?php

namespace Group\ASync\Pool;

use Group\ASync\Pool\MysqlPool;

class MysqlPoolServiceProvider extends \ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return object
     */
    public function register()
    {
        $this->app->singleton('mysqlPool', function () {
            return new MysqlPool();
        });
    }

    public function getName()
    {
        return 'mysqlPool';
    }
}