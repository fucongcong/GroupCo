<?php

return array(

    //key 必须是要唯一的 否则会自动合并
    'admin'=>[
        'pattern' => '/admin',
        'controller' => 'Admin:Home:Default:index',
    ],

    'node_add'=>[
        'pattern' => '/node/add',
        'controller' => 'Admin:Index:Index:addNode',
    ],

    'node_remove'=>[
        'pattern' => '/node/remove',
        'controller' => 'Admin:Index:Index:removeNode',
    ],

    'node_close'=>[
        'pattern' => '/node/close',
        'controller' => 'Admin:Index:Index:closeNode',
    ],

    'node_delete'=>[
        'pattern' => '/node/delete',
        'controller' => 'Admin:Index:Index:deleteNode',
    ],
);
