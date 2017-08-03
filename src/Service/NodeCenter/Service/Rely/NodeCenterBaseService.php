<?php

namespace src\Service\NodeCenter\Service\Rely;

use Group\Sync\Service;

abstract class NodeCenterBaseService extends Service
{
    public function getNodeCenterDao()
    {
        return $this->createDao("NodeCenter:NodeCenter");
    }
}