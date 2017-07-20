<?php

namespace src\Web\Listeners;

use Listener;
use Event;

class ServiceCallListener extends Listener
{
    public function setMethod()
    {
        return 'onServiceCall';
    }

    public function onServiceCall(Event $event)
    {
        $data = $event->getProperty();
        $cmd = $data['cmd'];
        $calltime = $data['calltime'];

        //上报监控平台
        
    }
}

