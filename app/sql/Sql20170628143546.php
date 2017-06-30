<?php

namespace app\sql;

use Group\Dao\SqlMigration;

class Sql20170628143546 extends SqlMigration
{
    public function run()
    {
        $this->addSql("CREATE TABLE `user` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `mobile` VARCHAR(20) NOT NULL , `password` VARCHAR(50) NOT NULL , PRIMARY KEY (`id`), UNIQUE `m-un` (`mobile`)) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
    }

    public function back()
    {

    }
}
