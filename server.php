<?php

define('ASYNC', FALSE);

$loader = require __DIR__.'/vendor/autoload.php';
$loader->setUseIncludePath(true);

$kernal = new \Group\SwooleKernal();
$kernal->init(realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR, $loader);


