<?php

return array(

    'homepage'=>[
    	'pattern' => '/',
    	'controller' => 'Web:Home:Default:index',
    ],

    'seckill' =>[
        'pattern' => '/seckill/{userId}',
        'controller' => 'Web:Home:Default:seckill',
    ],
);
