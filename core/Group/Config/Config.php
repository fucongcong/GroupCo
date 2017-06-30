<?php

namespace Group\Config;

use Exception;
use Group\Contracts\Config\Config as ConfigContract;

class Config implements ConfigContract
{
    private static $instance;

    protected $config = [];

    /**
     * 获取config下得值
     *
     * @param  configName,  name::key
     * @return string
     */
    public static function get($configName)
    {
        return  self::getInstance()->read($configName);
    }

    /**
     * 设置config下得值
     *
     * @param  key
     * @param  subKey
     * @param  value
     */
    public static function set($key, $subKey, $value)
    {
        self::getInstance()->setCustom($key, $subKey, $value);
    }

    /**
     * read config
     *
     * @param  configName,  name::key
     * @return array
     */
    public function read($configName)
    {
        $configName = explode('::', $configName);

        if (count($configName) == 2) {
            $config = $this->checkConfig($configName[0]);
            return $config[$configName[0]][$configName[1]];
        }

        return array();

    }

    /**
     * 设置config
     *
     * @param  array config
     */
    public function setConfig($config)
    {
        $this->config = array_merge($this->config, $config);
    }

    public function setCustom($key, $subKey, $value)
    {
        $this->config[$key][$subKey] = $value;
    }

    /**
     * 获取config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * return single class
     *
     * @return Group\Config Config
     */
    public static function getInstance(){

        if (!(self::$instance instanceof self)){
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function checkConfig($key)
    {
        $config = $this->config;

        if (!isset($config[$key])) {
            $app = require("config/".$key.".php");
            $this->config = array_merge($this->config, [$key => $app]);
        }

        return $this->config;
    }
}
