<?php

namespace Group\Routing;

use Group\Common\ArrayToolkit;
use Container;
use Group\Contracts\Routing\Route as RouteContract;

Class RouteService implements RouteContract
{
    /**
     * container
     *
     * @var Group\Container\Container
     */
    protected $container;

    /**
     * action name
     *
     * @var string
     */
    protected $action;

    /**
     * uri
     *
     * @var string
     */
    protected $uri;

    /**
     * the methods server
     *
     * @var string
     */
    protected $methods;

    /**
     * the method current
     *
     * @var string
     */
    protected $currentMethod;

    /**
     * routing
     *
     * @var array
     */
    protected $routing;

    /**
     * the parameters
     *
     * @var array
     */
    protected $parameters;

    /**
     * the parametersName
     *
     * @var array
     */
    protected $parametersName;

    private static $instance;

    /**
     * Set the container instance on the route.
     *
     * @param  \Group\Container\Container  $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * set params
     *
     * @param parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * get params
     *
     * @return params name
     */
    public function getParametersName()
    {
        return $this->parametersName ;
    }

    /**
     * set parametersName
     *
     * @param parameters name
     */
    public function setParametersName($parametersName)
    {
        $this->parametersName = $parametersName;
    }

    /**
     * get parameters
     *
     * @return parameters
     */
    public function getParameters()
    {
        return $this->parameters ;
    }

    /**
     * set action
     *
     * @param action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * get action
     *
     * @return action
     */
    public function getAction()
    {
        return $this->action ;
    }

    /**
     * set uri
     *
     * @param uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * get uris
     *
     * @return uris
     */
    public function getUri()
    {
        return $this->uri ;
    }

    /**
     * set methods
     *
     * @param methods
     */
    public function setMethods($methods)
    {
        $this->methods = $methods;
    }

    /**
     * get methods
     *
     * @return methods
     */
    public function getMethods()
    {
        return $this->methods ;
    }

    /**
     * set current method
     *
     * @param method
     */
    public function setCurrentMethod($method)
    {
        $this->currentMethod = strtoupper($method);
    }

    /**
     * get current methods
     *
     * @return method
     */
    public function getCurrentMethod()
    {
        return $this->currentMethod ;
    }

    public function setRouting($routing)
    {
        $this->routing = $routing;
    }

    public function getRouting()
    {
        return $this->routing ;
    }
    
    /**
     * 根据别名反解析路由
     *
     * @return method
     */
    public function deParse($alias, $parameters = [])
    {
        $routing = $this->routing;

        if(!isset($routing[$alias])) return false;

        $pattern = $routing[$alias]['pattern'];
        foreach ($parameters as $key => $parameter) {
            $pattern = str_replace("{".$key."}", $parameter, $pattern);
        }

        return $pattern;
    }

    /**
     * return single class
     *
     * @return Group\Routing Route
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof self)){
            self::$instance = new self;
        }

        return self::$instance;
    }
}
