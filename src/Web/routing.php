<?php

return array(

    'homepage'=>[
    	'pattern' => '/',
    	'controller' => 'Web:Home:Default:index',
    ],

    'demo'=>[
        'pattern' => '/demo',
        'controller' => 'Web:User:User:demo',
    ],

    'register'=>[
        'pattern' => '/register',
        'controller' => 'Web:User:User:register',
    ],

    'login'=>[
        'pattern' => '/login',
        'controller' => 'Web:User:User:login',
    ],

    'logout'=>[
        'pattern' => '/logout',
        'controller' => 'Web:User:User:logout',
    ],

    'group'=>[
    	'pattern' => '/group/{id}',
    	'controller' => 'Web:Group:Group:test',
    	'methods' => 'GET',
    ],

    'create_group'=>[
        'pattern' => '/group/{id}',
        'controller' => 'Web:Group:Group:add',
        'methods' => 'POST',
    ],

    'user_group'=>[
    	'pattern' => '/user/{id}/group/{groupId}',
    	'controller' => 'Web:Group:Group:test',
    	'methods' => 'GET',
    ],
);
