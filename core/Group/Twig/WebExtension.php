<?php

namespace Group\Twig;

use Twig_Extension;
use Route;
use Group\Routing\Router;

class WebExtension extends Twig_Extension
{
    /**
     * 重写Twig_Extension getFunctions方法
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'assets' => new \Twig_Function_Method($this, 'getPublic'),
            'dump'  => new \Twig_Function_Method($this, 'dump'),
            'render'  => new \Twig_Function_Method($this, 'render', array('is_safe' => array('html'))),
            'csrf_token'  => new \Twig_Function_Method($this, 'getCsrfToken'),
        );
    }

    public function getFilters ()
    {
        return array(
            'smart_time' => new \Twig_Filter_Method($this, 'smarttimeFilter'),
        );
    }

    /**
     * 获取asset目录下得文件路径
     *
     * @return string
     */
    public function getPublic($url)
    {
        return "/assets/".$url;
    }

    /**
     * 在模板调试使用
     *
     * @param var
     * @return void
     */
    public function dump($var)
    {
        return var_dump($var);
    }

    // public function render($controller, $params)
    // {   
    //     $config['controller'] = $controller;
    //     $config['parameters'] = $params;
    //     return app()->router->getTpl($config);
    // }

    public function smarttimeFilter($time) {
        $diff = time() - $time;
        if ($diff < 0) {
            return '未来';
        }

        if ($diff == 0) {
            return '刚刚';
        }

        if ($diff < 60) {
            return $diff . '秒前';
        }

        if ($diff < 3600) {
            return round($diff / 60) . '分钟前';
        }

        if ($diff < 86400) {
            return round($diff / 3600) . '小时前';
        }

        if ($diff < 2592000) {
            return round($diff / 86400) . '天前';
        }

        if ($diff < 31536000) {
            return date('m-d', $time);
        }

        return date('Y-m-d', $time);
    }

    public function getName()
    {
        return 'group_twig';
    }
}
