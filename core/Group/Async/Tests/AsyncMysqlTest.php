<?php

namespace Group\Async\Tests;

use Test;
use AsyncMysql;

class AsyncMysqlTest extends Test
{
    public function unitquery()
    {   
        $res = (yield AsyncMysql::query("INSERT INTO `user` (`id`, `mobile`, `password`) VALUES (NULL, '187681343332', '11111')"));
        
        if ($res) {
            $result = $res->getResult();
            $affectedRows = $res->getAffectedRows();
            $id = $res->getInsertId();
            $res = (yield AsyncMysql::query("DELETE FROM `user` WHERE id = {$id}"));
        }

        $res = (yield AsyncMysql::query("INSERT INTO `user` (`id`, `mobile`, `password`) VALUES (NULL, '187681343332', '11111')", false));

        if ($res) {
            $result = $res->getResult();
            $affectedRows = $res->getAffectedRows();
            $id = $res->getInsertId();
            $res = (yield AsyncMysql::query("DELETE FROM `user` WHERE id = {$id}", false));
        }

        
        // try {
        //     AsyncMysql::begin();
        //     $res = (yield AsyncMysql::query("INSERT INTO `user` (`id`, `mobile`, `password`) VALUES (NULL, '187681343332', '11111')"));
        
        //     if ($res) {
        //         $result = $res->getResult();
        //         $affectedRows = $res->getAffectedRows();
        //         $id = $res->getInsertId();
        //         $res = (yield AsyncMysql::query("DELETE FROM `user` WHERE id = {$id}"));
        //     }

        //     AsyncMysql::commit();
        // } catch (Exception $e) {
        //     AsyncMysql::rollback();
        // }

        // app('mysqlPool')->close();
    }
}
