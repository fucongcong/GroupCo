<?php

namespace src\Service\Monitor\Service;

use src\Service\Monitor\Service\Rely\MonitorBaseService;
use Api\Monitor\MonitorService;
use FileCache;

class MonitorServiceImpl extends MonitorBaseService implements MonitorService
{
    public function add($info)
    {
        $info['calltime'] = round($info['calltime'], 5);
        if (is_null($info['error'])) $info['error'] = '';

        $date = date("YmdH");
        FileCache::set("{$date}.log", json_encode($info)."\n", "runtime/monitor", FILE_APPEND);
        return true;
        //return $this->getMonitorDao()->add($info);
    }
}