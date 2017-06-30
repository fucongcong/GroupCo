<?php
/*
 * 单元测试入口
 */
use Group\App\App;

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->setUseIncludePath(true);

define('__ROOT__', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "../");

$app = new App();
$app -> initSelf();
$app -> registerServices();
$app -> singleton('container') -> setAppPath(__ROOT__);