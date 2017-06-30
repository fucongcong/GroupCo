<?php

namespace Group\Contracts\Container;

interface Container
{
    /**
     * build a moudle class
     *
     * @param  class
     * @return ReflectionClass class
     */
    public function buildMoudle($class);

    /**
     * do the moudle class action
     *
     * @param  class
     * @param  action
     * @param  array parameters
     * @return string
     */
    public function doAction($class, $action, array $parameters, \Request $request);

    /**
     * 设置时区
     *
     */
    public function setTimezone();

    /**
     * 获取当前时区
     *
     */
    public function getTimezone();

    /**
     * 设置环境
     *
     *@return string prod｜dev
     */
    public function getEnvironment();

    /**
     * 获取当前环境
     *
     */
    public function setEnvironment();

    /**
     * 设置系统根目录
     *
     */
    public function setAppPath($path);

    /**
     * 获取系统根目录
     *
     *@return string
     */
    public function getAppPath();

    /**
     * 设置地区
     *
     */
    public function setLocale();

    /**
     * 获取设置的地区
     *
     *@return string
     */
    public function getLocale();

    /**
     * 设置response
     *
     */
    public function setResponse($response);

    /**
     * 获取设置的response
     *
     *@return string
     */
    public function getResponse();

    /**
     * 设置request
     *
     */
    public function setRequest(\Request $request);

    /**
     * 获取设置的request
     *
     *@return string
     */
    public function getRequest();

    public function runningInConsole();

    public function isDebug();
}
