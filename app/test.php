<?php
/*
 * 单元测试入口
 */
use Group\App\App;

define('ASYNC', TRUE);
define('__ROOT__', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "../");

$loader = require __DIR__.'/../vendor/autoload.php';

$app = new App();
$app -> init();