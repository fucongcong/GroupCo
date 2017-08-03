<?php

namespace app\sql;

use Group\Sync\Dao\SqlMigration;

class Sql20170718073556 extends SqlMigration
{
    public function run()
    {
        $this->addSql("ALTER TABLE `nodes` ADD `serverName` VARCHAR(50) NOT NULL AFTER `status`;");
        $this->addSql("ALTER TABLE `nodes` ADD `serviceStatus` VARCHAR(50) NOT NULL DEFAULT 'offline' AFTER `status`;");
        $this->addSql("ALTER TABLE `nodes` ADD UNIQUE `ip-port` (`port`, `ip`);");
        $this->addSql("ALTER TABLE `nodes` ADD INDEX `s-s-s` (`status`, `serviceStatus`, `services`);");
    }

    public function back()
    {

    }
}
