<?php

namespace src\Service\Shop\Service\Rely;

use Group\Sync\Service;

abstract class ShopBaseService extends Service
{
    public function getShopDao()
    {
        return $this->createDao("Shop:Shop");
    }
}