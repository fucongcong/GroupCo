<?php

namespace Group\Contracts\Routing;

interface Router
{
    /**
     * match the uri
     *
     * @return  void
     */
    public function match();

    /**
     * preg the url
     *
     * @param  matches
     * @param  route_key
     * @param  array routing
     * @return  array|bool false
     */
    public function pregUrl($matches, $route_key, $routing);

    /**
     * do the controller
     *
     * @param  routing config
     * @return string
     */
    public function controller($config);

    /**
     * set the route
     *
     * @param  methods
     * @param  uri
     * @param  currentMethod
     */
    public function setRoute($methods, $uri, $currentMethod);

}
