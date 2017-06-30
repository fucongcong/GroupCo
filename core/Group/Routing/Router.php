<?php

namespace Group\Routing;

use Group\Common\ArrayToolkit;
use Group\Contracts\Routing\Router as RouterContract;
use App;
use Group\Events\KernalEvent;
use Group\Events\HttpEvent;
use Group\Container\Container;
use Response;

Class Router implements RouterContract
{
    /**
     * 支持的http方法
     *
     * @var methods
     */
    protected $methods = ["GET", "PUT", "POST", "DELETE", "HEAD", "PATCH"];

    /**
     * Route object
     *
     * @var route
     */
    protected $route;

    /**
     * Container object
     *
     * @var container
     */
    protected $container;

    /**
     * 初始化
     *
     * @param Container container
     * @param Request request
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $request = $this->container->getRequest();
        $this->setRoute($this->methods, $request->getPathInfo(), $request->getMethod());
    }

    /**
     * match the uri
     *
     */
    public function match()
    {
        $requestUri = $this->route->getUri();

        $routing = $this->getRouting();

        if (isset($routing[$requestUri])) {
            yield $this->controller($routing[$requestUri]);
            return;
        }

        foreach ($routing as $routeKey => $route) {
            preg_match_all('/{(.*?)}/', $routeKey, $matches);

            $config = "";

            if ($matches[0]) {
                $config = $this->pregUrl($matches, $routeKey, $routing);
            }

            if ($config) {
                yield $this->controller($config);
                return;
            }
        }

        $this->container->singleton('eventDispatcher')->dispatch(KernalEvent::NOTFOUND, new HttpEvent($this->container->getRequest(), null, $this->container->getSwooleResponse()));       
    }

    /**
     * preg the url
     *
     * @param  matches
     * @param  routeKey
     * @param  array routing
     * @return  array|bool false
     */
    public function pregUrl($matches, $routeKey, $routing)
    {   
        $requestUri = $this->route->getUri();
        if (substr($requestUri, -1) == "/") {
            $requestUri = substr($requestUri, 0, -1);
        }
        $countKey = explode("/", $requestUri);
        $countKeyPreg = explode("/", $routeKey);

        if (count($countKey)!= count($countKeyPreg)) {
            return false;
        }

        $route = $routeKey;
        foreach ($matches[0] as $key => $match) {
            $regex = str_replace($match, "(\S+)", $routeKey);
            $routeKey = $regex;

            $regex = str_replace("/", "\/", $regex);

            $parameters[] = $match;

        }

        foreach ($matches[1] as $key => $match) {
            $filterParameters[] = $match;
        }

        $this->route->setParametersName($filterParameters);

        if (preg_match_all('/^'.$regex.'$/', $this->route->getUri(), $values)) {
            $config = $routing[$route];
            $config['parameters'] = $this->mergeParameters($filterParameters, $values);
            return  $config;
        }

        return false;
    }

    /**
     * do the controller
     *
     * @param  routing config
     */
    public function controller($config)
    {   
        $tplData = (yield $this->getTpl($config));

        if ($tplData instanceof Response || $tplData instanceof \RedirectResponse || $tplData instanceof \JsonResponse) {
            $this->container->setResponse($tplData);
        } else {
            $this->container->setResponse(new Response($tplData));
        }
    }

    public function getTpl($config)
    {   
        list($group, $subGroup, $controller, $action) = explode(':', $config['controller']);
        $className = 'src\\'.$group.'\\Controller\\'.$subGroup.'\\'.$controller.'Controller';
        $action = $action.'Action';

        $this->route->setAction($action);
        $this->route->setParameters(isset($config['parameters']) ? $config['parameters'] : array());

        return $this->container->doAction($className, $action, isset($config['parameters']) ? $config['parameters'] : array(), $this->container->getRequest());
    }

    protected function mergeParameters($parameters, $values)
    {
        foreach ($parameters as $key => $parameter) {
            $parameterValue[$parameter] = $values[$key+1][0];
        }

        return $parameterValue;
    }

    //to do refactor me
    protected function getRouting()
    {
        $routing = $this->checkMethods();

        return $routing;
    }

    protected function checkMethods()
    {
        if ($this->container->getEnvironment() == "prod") {
            return $this->getMethodsCache();
        }

        $config = $this->createMethodsCache();

        return $config;
    }

    /**
     * set the route
     *
     * @param  methods
     * @param  uri
     */
    public function setRoute($methods, $uri, $method)
    {   
        $this->route = $this->container->singleton('route');
        $this->route->setMethods($methods);
        $this->route->setCurrentMethod($method);
        $this->route->setUri($uri);
        $this->route->setRouting($this->getRoutingConfig());
    }

    private function getMethodsCache()
    {
        $file = 'route/routing_'.$this->route->getCurrentMethod().'.php';

        if(\FileCache::isExist($file)) {
            return \FileCache::get($file);
        }

        $config = $this->createMethodsCache();
        \FileCache::set($file, $config);

        return $config;
    }

    /**
     * create routing cache
     *
     * @return  array
     */
    private function createMethodsCache()
    {   
        $routing = $this->route->getRouting();

        $config = array();

        foreach ($routing as $key => $route) {
                $route['alias'] = $key;

                if(!isset($route['methods'])) {
                    $config[$key] = $route;
                    continue;
                }

                if(isset($route['methods']) && !in_array(strtoupper($route['methods']), $this->methods)) continue;

                if(isset($route['methods']) && $this->route->getCurrentMethod() != strtoupper($route['methods']) ) continue;

                $config[$key] = $route;
        }

        $config = ArrayToolkit::index($config, 'pattern');

        return $config;
    }

    private function getRoutingConfig()
    {   
        $file = 'route/routing.php';

        if ($this->container->getEnvironment() == "prod") {
            if(\FileCache::isExist($file)) {
                return \FileCache::get($file);
            }
        }

        $sources = \Config::get('routing::source');

        $routings = [];
        foreach ($sources as $source) {
            $routing = include "src/{$source}/routing.php";
            if ($routing) {
                $routings = array_merge($routings, $routing);
            }
        }   
    
        \FileCache::set($file, $routings);
    
        return $routings;
    }
}
