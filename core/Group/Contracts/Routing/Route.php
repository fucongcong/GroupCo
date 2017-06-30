<?php

namespace Group\Contracts\Routing;

interface Route
{
    /**
     * set container
     *
     * @param Group\Container\Container container
     */
    public function setContainer(\Container $container);

    /**
     * set params
     *
     * @param parameters
     */
    public function setParameters($parameters);

    /**
     * get params
     *
     * @return params name
     */
    public function getParametersName();

    /**
     * set parametersName
     *
     * @param parameters name
     */
    public function setParametersName($parametersName);

    /**
     * get parameters
     *
     * @return parameters
     */
    public function getParameters();

    /**
     * set action
     *
     * @param action
     */
    public function setAction($action);

    /**
     * get action
     *
     * @return action
     */
    public function getAction();

    /**
     * set uri
     *
     * @param uri
     */
    public function setUri($uri);

    /**
     * get uris
     *
     * @return uris
     */
    public function getUri();

    /**
     * set methods
     *
     * @param methods
     */
    public function setMethods($methods);

    /**
     * get methods
     *
     * @return methods
     */
    public function getMethods();

    /**
     * set current method
     *
     * @param method
     */
    public function setCurrentMethod($method);

    /**
     * get current methods
     *
     * @return method
     */
    public function getCurrentMethod();

        /**
     * 根据别名反解析路由
     *
     * @return method
     */
    public function deParse($alias, $parameters = []);
}
