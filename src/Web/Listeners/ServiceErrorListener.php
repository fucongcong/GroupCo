<?php

namespace src\Web\Listeners;

use Listener;
use Event;

class ServiceErrorListener extends Listener
{
    public function setMethod()
    {
        return 'onServiceError';
    }

    public function onServiceError(Event $event)
    {
        $data = $event->getProperty();
    }
}

