<?php

namespace Group\Console\Command;

use Group\Console\Command as Command;
use Group\Console\Command\SqlCleanCommand as SqlCleanCommand;
use Group\Common\ArrayToolkit;

class SqlRollBackCommand extends Command
{
    protected $fileList = [];

    protected $toVersion;

    protected $versions = [];

    protected $dao;

    public function init()
    {
        $this->dao = new \Dao();
        $input = $this->getArgv();
        $this->toVersion = isset($input[0]) ? $input[0] : null;

        $versions = $this->doSql($this->getMigrations(), false)->fetchAll();
        $this->versions = array_values(ArrayToolkit::column($versions, "version"));

        if (!in_array($this->toVersion, $this->versions)) $this->error('找不到指定的版本');

        $this->ListSql(__ROOT__."app/sql/");
    }

    private function ListSql($sqlDir)
    {   
        $files = [];
        if (is_dir($sqlDir)) {
            $dir = opendir($sqlDir);
            while (($file = readdir($dir)) !== false) {
                $file = explode(".", $file);
                $fileName = $file[0];

                if ($fileName && isset($file[1]) && $file[1] == "php") {
                    $files[] = $fileName;     
                }
            }
            closedir($dir);
        }

        krsort($files);
        foreach ($files as $fileName) {
            if ($fileName >= $this->toVersion && in_array($fileName, $this->versions)) {
                $migrateClass = "\\app\\sql\\".$fileName;
                $sqlMigrate = new $migrateClass;
                $sqlMigrate->back();
                $sqlArr = $sqlMigrate->getSqlArr();

                $this->startRollBack($sqlArr);

                $this->doSql($this->dropVersion($fileName), false);
            }
        }
    }

    private function startRollBack($sqlArr)
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

    private function dropVersion($version)
    {
        return "DELETE FROM `migration_versions` WHERE version = '{$version}'";
    }

    private function getMigrations()
    {
        return "SELECT * FROM `migration_versions`";
    }
}
