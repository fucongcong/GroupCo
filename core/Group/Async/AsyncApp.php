<?php

namespace Group\Async;

use Group\Handlers\AliasLoaderHandler;
use Group\Config\Config;
use Group\Handlers\ExceptionsHandler;
use Group\Events\HttpEvent;
use Group\Events\KernalEvent;
use Group\Cache\BootstrapClass;
use Group\Container\Container;

class AsyncApp
{
    /**
     * array instances
     *
     */
    protected $instances;

    private static $instance;

    public $container;

    public $router;

    /**
     * array aliases
     *
     */
    protected $aliases = [
        'App'               => 'Group\Async\AsyncApp',
        'Cache'             => 'Group\Cache\Cache',
        'Config'            => 'Group\Config\Config',
        'Container'         => 'Group\Async\Container\Container',
        'Dao'               => 'Group\Dao\Dao',
        'Filesystem'        => 'Group\Common\Filesystem',
        'FileCache'         => 'Group\Async\Cache\FileCache',
        'StaticCache'       => 'Group\Async\Cache\StaticCache',
        'Service'           => 'Group\Async\Services\Service',
        'ServiceProvider'   => 'Group\Async\Services\ServiceProvider',
        'Test'              => 'Group\Test\Test',
        'Log'               => 'Group\Log\Log',
    ];

    /**
     * array singles
     *
     */
    protected $singles = [
        'dao' => 'Group\Dao\Dao',
    ];

    protected $serviceProviders = [
        'Group\Async\Redis\RedisServiceProvider',
        'Group\Async\Cache\FileCacheServiceProvider',
        'Group\Async\Cache\StaticCacheServiceProvider',
        'Group\Async\Cache\CacheServiceProvider',
    ];

    public function __construct()
    { 
        $this->aliasLoader();

        $this->doSingle();

        $this->doSingleInstance();
    }

    /**
     * do the class alias
     *
     */
    public function aliasLoader()
    {
        $aliases = Config::get('app::aliases');
        $this->aliases = array_merge($aliases, $this->aliases);
        AliasLoaderHandler::getInstance($this->aliases)->register();

    }

    /**
     *  向App存储一个单例对象
     *
     * @param  name，callable
     * @return object
     */
    public function singleton($name, $callable = null)
    {
        if (!isset($this->instances[$name]) && $callable) {
            $this->instances[$name] = call_user_func($callable);
        }

        return $this->instances[$name];
    }

    /**
     *  在网站初始化时就已经生成的单例对象
     *
     */
    public function doSingle()
    {   
        foreach ($this->singles as $alias => $class) {
            $this->instances[$alias] = new $class();
        }
    }

    public function doSingleInstance()
    {
        $this->instances['container'] = Container::getInstance();
    }

    /**
     *  注册服务
     *
     */
    public function registerServices()
    {   
        //$this->setServiceProviders();

        foreach ($this->serviceProviders as $provider) {
            $provider = new $provider(self::$instance);
            $provider->register();
        }
    }

    /**
     * return single class
     *
     * @return core\App\App App
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof self)){
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function initSelf()
    {
        self::$instance = $this;
    }

    public function rmInstances($name)
    {
        if(isset($this->instances[$name]))
            unset($this->instances[$name]);
    }

    /**
     * 类文件缓存
     *
     * @param loader
     */
    public function doBootstrap($loader) 
    {   
        $this->setServiceProviders();

        // if (Config::get('app::environment') == "prod" && is_file("runtime/cache/bootstrap.class.cache")) {
        //     require "runtime/cache/bootstrap.class.cache";
        //     return;
        // }

        // $bootstrapClass = new BootstrapClass($loader);
        // foreach ($this->serviceProviders as $serviceProvider) {
        //     $bootstrapClass->setClass($serviceProvider);
        // }
        // foreach ($this->bootstraps as $bootstrap) {
        //     $bootstrap = isset($this->aliases[$bootstrap]) ? $this->aliases[$bootstrap] : $bootstrap;
        //     $bootstrapClass->setClass($bootstrap);
        // }
        // $bootstrapClass->bootstrap();
    }

    /**
     * set ServiceProviders
     *
     */
    public function setServiceProviders()
    {
        
    }

    /**
     * ingore ServiceProviders
     *
     */
    public function ingoreServiceProviders($provider)
    {   
        foreach ($this->serviceProviders as $key => $val) {
            if ($val == $provider) unset($this->serviceProviders[$key]);
        } 
    }

    /**
     * 处理一个抽象对象
     * @param  string  $abstract
     * @return mixed
     */
    public function make($abstract)
    {
        //如果是已经注册的单例对象
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $reflector = app('container')->buildMoudle($abstract);
        if (!$reflector->isInstantiable()) {
            throw new Exception("Target [$concrete] is not instantiable!");
        }

        //有单例
        if ($reflector->hasMethod('getInstance')) {
            $object = $abstract::getInstance();
            $this->instances[$abstract] = $object;
            return $object;
        }

        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            return new $abstract;
        }

        return null;
    }
}
