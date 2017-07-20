<?php

namespace src\Service\Monitor\Service\Impl;

use src\Service\Monitor\Service\Rely\MonitorBaseService;
use src\Service\Monitor\Service\MonitorService;

class MonitorServiceImpl extends MonitorBaseService implements MonitorService
{
    public function add($info)
    {
        $info['calltime'] = round($info['calltime'], 5);
        if (is_null($info['error'])) $info['error'] = '';
        return $this->getMonitorDao()->add($info);
    }
}