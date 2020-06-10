<?php

define('ASYNC', false);
define('__ROOT__', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

$loader = require __DIR__.'/vendor/autoload.php';

$kernal = new src\WebSocket\Server();
$kernal->init();


