<?php

namespace Group\Listeners;

use Group\Events\HttpEvent;
use Group\Events\KernalEvent;
use Group\Session\CsrfSessionService;

class KernalRequestListener extends \Listener
{
    public function setMethod()
    {
        return 'onKernalRequest';
    }

    public function onKernalRequest(\Event $event)
    {	
    	//$request = $event->getRequest();
    }
}
