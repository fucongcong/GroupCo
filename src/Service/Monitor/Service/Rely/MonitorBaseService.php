<?php

namespace src\Service\Monitor\Service\Rely;

use Group\Sync\Service;

abstract class MonitorBaseService extends Service
{
    public function getMonitorDao()
    {
        return $this->createDao("Monitor:Monitor");
    }
}