<?php

namespace Group\Dao\Tests;

use Test;
use AsyncMysql;

class AsyncMysqlTest extends Test
{
    public function unitquery()
    {   
        $res = (yield AsyncMysql::query("INSERT INTO `user` (`id`, `mobile`, `password`) VALUES (NULL, '18768122222', '11111')"));
        
        if ($res) {
            $result = $res->getResult();
            $affectedRows = $res->getAffectedRows();
            $id = $res->getInsertId();
        }
        //dump($result);
        $res = (yield AsyncMysql::query("DELETE FROM `user` WHERE id = {$id}"));
        //dump($res);
    }
}
