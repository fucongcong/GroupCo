<?php

namespace Group\Console;

abstract class Command
{
    protected $argv;

    /**
     * 定义的脚本执行方法
     *
     */
    abstract function init();

    /**
     * 设置参数
     *
     * @param  argv(array)
     */
    public function setArgv($argv)
    {
        array_shift($argv);
        array_shift($argv);
        $this->argv = $argv;
    }

    /**
     * 获取参数
     *
     * @return  argv(array)
     */
    public function getArgv()
    {
        return $this->argv;
    }

    /**
     * 输出文本
     *
     */
    public function outPut($info)
    {
        echo $info.PHP_EOL;
    }

    /**
     * 错误提示
     *
     */
    public function error($error)
    {
        die($error.PHP_EOL);
    }
}
