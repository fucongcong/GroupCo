<?php

namespace app\sql;

use Group\Sync\Dao\SqlMigration;

class Sql20170720164604 extends SqlMigration
{
    public function run()
    {
        $this->addSql("CREATE TABLE `monitor` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `server` VARCHAR(50) NOT NULL COMMENT '服务分组' , `service` VARCHAR(50) NOT NULL COMMENT '服务名' , `action` VARCHAR(50) NOT NULL COMMENT '方法' , `calltime` INT NOT NULL COMMENT '响应事件' , `ip` VARCHAR(50) NOT NULL , `port` INT(10) UNSIGNED NOT NULL , `error` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");

        $this->addSql("ALTER TABLE `monitor` ADD INDEX `ip-port` (`ip`, `port`);");

        $this->addSql("ALTER TABLE `monitor` ADD INDEX `server` (`server`);");

        $this->addSql("ALTER TABLE `monitor` ADD INDEX `service` (`service`);");

        $this->addSql("ALTER TABLE `monitor` ADD INDEX `action` (`action`);");

        $this->addSql("ALTER TABLE `monitor` CHANGE `calltime` `calltime` DECIMAL(10,5) NOT NULL COMMENT '响应时间'");

        $this->addSql("ALTER TABLE `monitor` CHANGE `error` `error` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '';");
    }

    public function back()
    {

    }
}
