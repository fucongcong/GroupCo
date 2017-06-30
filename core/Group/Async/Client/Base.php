<?php

namespace Group\Async\Client;

use Config;

class Base 
{	
	const CONNECT_ERROR = "connect_error";

	const CONNECT_TIMEOUT = "connect_timeout";

	public function __construct($ip, $port, $data, $timeout)
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->data = $data;
        $this->timeout = $timeout;
    }

    public function call(callable $callback)
    {

    }
}
