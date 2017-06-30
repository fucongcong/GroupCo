<?php

namespace Group\Console\Command;

use Group\Console\Command as Command;
use Group\Common\ArrayToolkit;

class SqlMigrateCommand extends Command
{
    protected $versions = [];

    protected $dao;

    public function init()
    {   
        $this->dao = new \Dao();
        $this->doSql($this->getInitSql(), false);
        $versions = $this->doSql($this->getMigrations(), false)->fetchAll();
        $this->versions = array_values(ArrayToolkit::column($versions, "version"));

        $this->ListSql(__ROOT__."app/sql/");
    }

    private function ListSql($sqlDir)
    {
        if (is_dir($sqlDir)) {
            $dir = opendir($sqlDir);

            while (($file = readdir($dir)) !== false) {
                $file = explode(".", $file);
                $fileName = $file[0];

                if ($fileName && isset($file[1]) && $file[1] == "php") {
                    $this->filterLockFile($fileName);
                }
            }
            closedir($dir);
        }
    }

    private function filterLockFile($file)
    {
        $versions = $this->versions;

        if (in_array($file, $versions)) return;

        $migrateClass = "\\app\\sql\\".$file;
        $sqlMigrate = new $migrateClass;
        $sqlMigrate->run();
        $sqlArr = $sqlMigrate->getSqlArr();

        $this->startMigrate($sqlArr);

        $this->doSql($this->insertVersion($file), false);
    }

    private function startMigrate($sqlArr)
    {
        foreach ($sqlArr as $sql) {
            $this->doSql($sql);
        }
    }

    private function doSql($sql, $needOutput = true)
    {   
        if ($needOutput) $this->outPut($sql);

        return $this->dao->querySql($sql, 'default');
    }

    private function getInitSql()
    {
        return "CREATE TABLE IF NOT EXISTS `migration_versions`( `version` VARCHAR(50) NOT NULL COMMENT '版本号' , UNIQUE `version-un` (`version`)) ENGINE = InnoDB;";
    }

    private function insertVersion($version)
    {
        return "INSERT INTO `migration_versions` (`version`) VALUES ('{$version}')";
    }

    private function getMigrations()
    {
        return "SELECT * FROM `migration_versions`";
    }
}
