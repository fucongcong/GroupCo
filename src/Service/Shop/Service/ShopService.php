<?php

namespace src\Service\Shop\Service;

interface ShopService
{
    public function getShop($id);
    
    public function addShop($data);

    public function editShop($id, $data);

    public function deleteShop($id);

    public function searchShop(array $conditions, array $orderBy, $start, $limit);
    
    public function searchShopCount(array $conditions);
}