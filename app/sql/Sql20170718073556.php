<?php

namespace app\sql;

use Group\Sync\Dao\SqlMigration;

class Sql20170718073556 extends SqlMigration
{
    public function run()
    {
        $this->addSql("ALTER TABLE `nodes` ADD `serverName` VARCHAR(50) NOT NULL AFTER `status`;");
    }

    public function back()
    {

    }
}
