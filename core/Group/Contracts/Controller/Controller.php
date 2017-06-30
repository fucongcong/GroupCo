<?php

namespace Group\Contracts\Controller;

interface Controller
{
    /**
     * 渲染模板的方法
     *
     * @param  string  $tpl
     * @param  array   $array
     * @return response
     */
    public function render($tpl, $array = array());

    /**
     * 实例化一个服务类
     *
     * @param  string  $serviceName
     * @return class
     */
    public function createService($serviceName);

    /**
     * route的实例
     *
     * @return Group\Routing\Route
     */
    public function route();

    /**
     * 获取容器
     *
     * @return Group\Container\Container
     */
    public function getContainer();

    public function redirect($url, $status = 302);
}
