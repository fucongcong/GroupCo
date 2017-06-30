<?php

namespace Group\Events;

final class QueueEvent extends \Event
{   
    const CRASH = "server.crash";

    protected $server;

    protected $host;

    public function __construct($server, $host)
    {   
        $this->server = $server;
        $this->host = $host;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function getHost()
    {
        return $this->host;
    }
}
