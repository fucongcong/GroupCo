<?php

namespace src\Service\NodeCenter\Dao\Impl;

use Dao;
use src\Service\NodeCenter\Dao\NodeCenterDao;

class NodeCenterDaoImpl extends Dao implements NodeCenterDao
{
    protected $table = "nodes";

    public function getActiveNodes()
    {
        $queryBuilder = $this->getDefault()->createQueryBuilder();
        $queryBuilder
            ->select("*")
            ->from($this->table)
            ->andwhere("status = 'active'")
            ->andwhere("serviceStatus = 'online'");
            
        return $queryBuilder->execute()->fetchAll();
    }
}
