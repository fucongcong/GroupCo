<?php

namespace Group\Listeners;

use Group\Events\HttpEvent;
use Group\Events\KernalEvent;

class NotFoundListener extends \Listener
{
    public function setMethod()
    {
        return 'onNotFound';
    }

    public function onNotFound(\Event $event)
    {   
        $container = $event->getContainer();
        $page = $container->singleton('twig')->render(\Config::get('view::notfound_page'));
        $response = new \Response($page, 404);
        $container->setResponse($response);
    }
}
