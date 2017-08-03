<?php

namespace src\Service\Monitor\Dao\Impl;

use Dao;
use src\Service\Monitor\Dao\MonitorDao;

class MonitorDaoImpl extends Dao implements MonitorDao
{
    protected $table = "monitor";

    public function add($info)
    {
        $conn = $this->getDefault();
        $affected = $conn->insert($this->table, $info);
        if ($affected <= 0) {
            return fasle;
        }
        return $conn->lastInsertId();
    }
}
