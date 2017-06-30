<?php

use Group\App\App;
use Group\Container\Container;
use \Group\Coroutine\SysCall;
use \Group\Coroutine\Task;
use \Group\Coroutine\Scheduler;

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
            return App::getInstance();
        }

        return App::getInstance()->make($abstract);
    }
}

if (!function_exists('ajax')) {
    /**
     * ajax return.返回一个json数组，并结束整个请求。
     *
     * @param  string  $message
     * @param  array     $data
     * @param  int   $code
     * @return void
     *
     */
    function ajax($message = '', $data = [], $code = 200)
    {
        app('container')->setResponse(new \JsonResponse(['message' => $message, 'data' => $data, 'code' => $code], 200));
        app()->handleHttp();
        exit;
    }
}

if (!function_exists('json')) {
    /**
     * 返回一个json response
     *
     * @param  array     $data
     * @param  int   $status
     * @param  array     $headers
     * @param  int   $options
     * @return object \JsonResponse
     *
     */
    function json($data = [], $status = 200, array $headers = [], $options = 0)
    {
        return new \JsonResponse($data, $status, $headers, $options);
    }
}

if (!function_exists('service')) {
    /**
     * 返回一个service对象
     *
     * @param  string     $serviceName
     * @return object
     *
     */
    function service($serviceName)
    {
        return app('service')->createService($serviceName);
    }
}

function getTaskId() {
    return new SysCall(function(Task $task){
        $task->send($task->getTaskId());
        $task->run();
    });
}

function getContainer() {
    return new SysCall(function(Task $task){
        $task->send($task->getContainer());
        $task->run();
    });
}


