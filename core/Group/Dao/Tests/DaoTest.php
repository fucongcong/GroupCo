<?php

namespace Group\Dao\Tests;

use Dao;
use Test;

class DaoTest extends Test
{   
    protected $dao;

    public function __initialize()
    {
        $this->dao = new Dao();
    }

    public function testQuerySql()
    {
        // $sql = "CREATE TABLE IF NOT EXISTS `groups` (
  //         `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  //         `title` varchar(255) NOT NULL,
  //         PRIMARY KEY (`id`)
  //       ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
        // ";
        // $this->dao->querySql($sql, 'default');


        // $sql = "INSERT INTO `Group`.`groups` (`id`,`title`) VALUES (31,:title);";
        // $bind = array('title' => "aa");
        // try{
        //     $dao = $this->dao->getDefault();
        //     $sts = $dao->prepare($sql);
        //     $sts->execute($bind);
        //     //$dao->execute($sts);
        //     echo $this->dao->getDefault()->lastInsertId();
        // }catch(\Exception $e){
        //     echo $e->getMessage();
        // }
        // $sql = "SELECT * FROM `Group`.`groups` WHERE id=:id LIMIT 0,1";
        // $bind = array('id' => 1);
        // $result = $this->dao->getRead()->fetchOne($sql, $bind);

        // $this->assertEquals('1222222', $result['title']);
    }
}