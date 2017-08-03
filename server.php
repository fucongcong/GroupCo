<?php

define('ASYNC', true);
define('__ROOT__', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

$loader = require __DIR__.'/vendor/autoload.php';

$kernal = new \Group\SwooleKernal();
$kernal->init();


