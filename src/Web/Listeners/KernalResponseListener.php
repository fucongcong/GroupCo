<?php

namespace src\Web\Listeners;

use Listener;
use Event;

class KernalResponseListener extends Listener
{
    public function setMethod()
    {
        return 'onKernalResponse';
    }

    public function onKernalResponse(Event $event)
    {
        echo 'this is a KernalResponse Listener';
    }
}

