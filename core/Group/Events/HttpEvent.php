<?php

namespace Group\Events;

final class HttpEvent extends \Event
{	
	protected $request;

    protected $response;

    protected $swooleHttpResponse;

    protected $container;

    public function __construct(\Request $request = null, $response = null, $swooleHttpResponse, $container = null)
    {	
    	$this->request = $request;
        $this->response = $response;
        $this->swooleHttpResponse = $swooleHttpResponse;
        $this->container = $container;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getSwooleHttpResponse()
    {
        return $this->swooleHttpResponse;
    }

    public function getContainer()
    {
        return $this->container;
    }
}
