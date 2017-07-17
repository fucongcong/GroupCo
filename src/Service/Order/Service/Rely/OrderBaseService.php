<?php

namespace src\Service\Order\Service\Rely;

use Group\Sync\Service;

abstract class OrderBaseService extends Service
{
    public function getOrderDao()
    {
        return $this->createDao("Order:Order");
    }
}