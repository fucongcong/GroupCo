<?php

namespace src\Service\Shop\Service\Impl;

use src\Service\Shop\Service\Rely\ShopBaseService;
use src\Service\Shop\Service\ShopService;

class ShopServiceImpl extends ShopBaseService implements ShopService
{
    public function getShop($id)
    {
        return $this->getShopDao()->getShop($id);
    }
    
    public function addShop($data)
    {
        return $this->getShopDao()->addShop($data);
    }

    public function editShop($id, $data)
    {
        return $this->getShopDao()->editShop($id, $data);
    }

    public function deleteShop($id)
    {
        return $this->getShopDao()->editShop($id, ['isDel' => 1]);
    }

    public function searchShop(array $conditions, array $orderBy, $start, $limit)
    {
        return $this->getShopDao()->searchShop($conditions, $orderBy, $start, $limit);
    }
    
    public function searchShopCount(array $conditions)
    {
        return $this->getShopDao()->searchShopCount($conditions);
    }
}