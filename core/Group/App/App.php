<?php

namespace Group\App;

use Group\Handlers\AliasLoaderHandler;
use Group\Config\Config;
use Group\Routing\Router;
use Group\Handlers\ExceptionsHandler;
use Group\Events\HttpEvent;
use Group\Events\KernalEvent;
use Group\Cache\BootstrapClass;
use Group\Container\Container;
use Group\Events\Event;
use Symfony\Component\HttpFoundation\ParameterBag;
use Group\Events\ExceptionEvent;

class App
{
    /**
     * array instances
     *
     */
    protected $instances;

    protected $routing;

    private static $instance;

    public $container;

    /**
     * array aliases
     *
     */
    protected $aliases = [
        'App'               => 'Group\App\App',
        'Config'            => 'Group\Config\Config',
        'Container'         => 'Group\Container\Container',
        'Controller'        => 'Group\Controller\Controller',
        'Event'             => 'Group\Events\Event',
        'Filesystem'        => 'Group\Common\Filesystem',
        'StaticCache'       => 'Group\Cache\StaticCache',
        'Request'           => 'Group\Http\Request',
        'Response'          => 'Group\Http\Response',
        'Cookie'            => 'Group\Http\Cookie',
        'JsonResponse'      => 'Group\Http\JsonResponse',
        'RedirectResponse'  => 'Group\Http\RedirectResponse',
        'Service'           => 'Group\Services\Service',
        'ServiceProvider'   => 'Group\Services\ServiceProvider',
        'Test'              => 'Group\Test\Test',
        'Listener'          => 'Group\Listeners\Listener',
        'Queue'             => 'Group\Queue\Queue',
        'AsyncMysql'        => 'Group\Async\AsyncMysql',
        'AsyncRedis'        => 'Group\Async\AsyncRedis',
        'AsyncFile'         => 'Group\Async\AsyncFile',
        'AsyncLog'          => 'Group\Async\AsyncLog',
        'AsyncService'      => 'Group\Async\AsyncService',
    ];

    /**
     * array singles
     *
     */
    protected $singles = [
    ];

    protected $onWorkStartServices = [
        'Group\Services\ServiceRegister',
        'Group\Cache\StaticCacheServiceProvider',
    ];

    protected $onRequestServices = [
        'Group\Routing\RouteServiceProvider',
        'Group\EventDispatcher\EventDispatcherServiceProvider',
    ];

    protected $names = [];

    public function __construct()
    { 
        $this->aliasLoader();

        $this->doSingle();
    }

    /**
     * init appliaction
     *
     */
    public function init()
    {
        $this->initSelf();
        $this->setServiceProviders();
        $this->registerOnWorkStartServices();
        $this->initRoutingConfig();
    }

    /**
     * terminate app
     *
     */
    public function terminate($request, $response)
    {   
        $container = (yield getContainer());
        $container->setAppPath(__ROOT__);

        $this->registerOnRequestServices($container);

        yield $container->singleton('eventDispatcher')->dispatch(KernalEvent::INIT, new Event($container));

        $request = new \Request($request->get, $request->post, [], $request->cookie
            , $request->files, $request->server);
        if (0 === strpos($request->headers->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded')
            && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), array('PUT', 'DELETE', 'PATCH'))
        ) {
            parse_str($request->getContent(), $data);
            $request->request = new ParameterBag($data);
        }

        $container->setSwooleResponse($response);
        yield $container->setRequest($request);
        
        $container->router = new Router($container, $this->routing);
        yield $container->router->match();

        yield $this->handleSwooleHttp($response);
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
        $singles = Config::get('app::singles');
        $this->singles = array_merge($singles, $this->singles);
        foreach ($this->singles as $alias => $class) {
            $this->instances[$alias] = new $class();
        }
    }

    public function registerOnWorkStartServices()
    {
        foreach ($this->onWorkStartServices as $provider) {
            $provider = new $provider(self::$instance);
            $provider->register();
        }
    }

    public function registerOnRequestServices($container)
    {
        foreach ($this->onRequestServices as $provider) {
            $provider = new $provider($container);
            $provider->register();
        }
    }

    public function getOnRequestServicesName()
    {   
        if (empty($this->names)) {
            $names = [];
            foreach ($this->onRequestServices as $provider) {
                $provider = new $provider(self::$instance);
                $names[] = $provider->getName();
            }

            $this->names = $names;
        }
        
        return $this->names;
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

    /**
     * 处理响应请求
     *
    */
    public function handleSwooleHttp($swooleHttpResponse)
    {   
        $container = (yield getContainer());
        $response = $container->getResponse();
        $request = $container->getRequest();

        yield $container->singleton('eventDispatcher')->dispatch(KernalEvent::RESPONSE, new HttpEvent($request, $response, $swooleHttpResponse));

        yield $this->release($container);
        unset($container);
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

    public function release($container)
    {
        $resources = ['redis', 'mysql'];
        foreach ($resources as $resource) {
            if (!is_null($container->singleton($resource))) {
                $container->singleton($resource)->close();
            }
        }
    }

    private function initRoutingConfig()
    {   
        $file = 'route/routing.php';
        $sources = Config::get('routing::source');

        $routings = [];
        foreach ($sources as $source) {
            $routing = include_once __ROOT__."src/{$source}/routing.php";
            if ($routing) {
                $routings = array_merge($routings, $routing);
            }
        }   
    
        $this->routing = $routings;
    }

    /**
     * set ServiceProviders
     *
     */
    public function setServiceProviders()
    {
        $onWorkStartServices = Config::get('app::onWorkStartServices');
        $this->onWorkStartServices = array_merge($onWorkStartServices, $this->onWorkStartServices);

        $onRequestServices = Config::get('app::onRequestServices');
        $this->onRequestServices = array_merge($onRequestServices, $this->onRequestServices);
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
