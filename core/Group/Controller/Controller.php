<?php

namespace Group\Controller;

use Group\Contracts\Controller\Controller as ControllerContract;
use Group\Exceptions\NotFoundException;
use Config;
use Cookie;
use Firebase\JWT\JWT;

class Controller implements ControllerContract
{
    protected $app;

    protected $container;

    public function __construct($app, $container)
    {
        $this->app = $app;
        $this->container = $container;
    }

    /**
     * 渲染模板的方法
     *
     * @param  string  $tpl
     * @param  array   $array
     * @return response
     */
    public function render($tpl, $array = array())
    {   
        return $this->container->singleton('twig')->render($tpl, $array);
    }

    public function twigInit()
    {   
        return $this->container->singleton('twig');
    }

    /**
     * 实例化一个服务类
     *
     * @param  string  $serviceName
     * @return class
     */
    public function createService($serviceName)
    {
        return $this->app->singleton('service')->createService($serviceName);
    }

    /**
     * route的实例
     *
     * @return Group\Routing\Route
     */
    public function route()
    {   
        return $this->container->singleton('route');
    }

    /**
     * 获取容器
     *
     * @return Group\Container\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    public function redirect($url, $status = 302)
    {   
        return new \Response('', $status, ['location' => $url]);
    }

    public function setJwt($request, $data, $response)
    {   
        $httpHost = Config::get('jwt::domain');
        $exprieTime = time() + Config::get('jwt::exprieTime');
        $token = array(
            'data' => $data,
            "iss" => $httpHost,
            "aud" => $httpHost,
            "iat" => time(),
            "exp" => $exprieTime
        );

        $jwt = JWT::encode($token, Config::get('jwt::privateKey'), 'RS256');
        $response->headers->setCookie(new Cookie('JWT', $jwt, $exprieTime, '/', $httpHost));

        return $response;
    }

    public function pasreJwt($request)
    {   
        $jwt = $request->cookies->get('JWT');
        $tks = explode('.', $jwt);
        if (count($tks) != 3) {
            return false;;
        }

        $data = JWT::decode($jwt, Config::get('jwt::publicKey'), array('RS256'));
        $data =  (array) $data;

        return $data['data'];
    }

    public function clearJwt($request, $response)
    {   
        $httpHost = Config::get('jwt::domain');
        $response->headers->clearCookie('JWT', '/', $httpHost);

        return $response;
    }

    public function __call($method, $parameters)
    {
        throw new NotFoundException("Method [$method] does not exist.");
    }
}
