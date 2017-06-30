<?php
/*
 * 单元测试入口
 */
use Group\App\App;

$loader = require __DIR__.'/../vendor/autoload.php';

$loader->setUseIncludePath(true);

$app = new App();

$app -> initSelf();

$app -> registerServices();
