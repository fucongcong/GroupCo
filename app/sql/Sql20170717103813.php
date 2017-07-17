<?php

namespace app\sql;

use Group\Sync\Dao\SqlMigration;

class Sql20170717103813 extends SqlMigration
{
    public function run()
    {
        $this->addSql("CREATE TABLE `nodes` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `ip` VARCHAR(50) NOT NULL , `port` INT(5) UNSIGNED NOT NULL , `status` VARCHAR(50) NOT NULL COMMENT '状态' , `services` VARCHAR(255) NOT NULL COMMENT '提供的服务' , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT = '节点';");
    }

    public function back()
    {

    }
}
