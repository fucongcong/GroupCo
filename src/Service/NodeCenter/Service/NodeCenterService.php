<?php

namespace src\Service\NodeCenter\Service;

interface NodeCenterService
{
	public function getService($serviceName);

    public function updateService();
}