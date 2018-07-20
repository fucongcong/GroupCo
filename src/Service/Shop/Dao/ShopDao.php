<?php

namespace src\Service\Shop\Dao;

interface ShopDao
{
    public function getShop($id);
    
    public function addShop($data);

    public function editShop($id, $data);

    public function searchShop(array $conditions, array $orderBy, $start, $limit);
    
    public function searchShopCount(array $conditions);
}

