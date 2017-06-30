<?php
return [

    //预先需要绑定的监听事件
    'services' => [

        //like
        // [
        //     'eventName' => 'kernal.response',
        //     'listener'  => 'src\web\Listeners\KernalResponseListener',
        //     'priority'  => 10,
        //  ]
        [
            'eventName' => 'kernal.request',
            'listener'  => 'src\Web\Listeners\KernalRequestListener',
            'priority'  => 10,
        ]

    ]
];
