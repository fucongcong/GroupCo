<?php

namespace Group\Dao;

abstract class SqlMigration
{
    protected $sqlArr = [];

    /**
     * run the sql
     *
     */
    abstract public function run();

    abstract public function back();

    /**
     * add sql
     *
     * @param  sql(string)
     */
    public function addSql($sql)
    {
        $this->setSqlArr($sql);
    }

    /**
     * get sql array
     *
     * @return array
     */
    public function getSqlArr()
    {
        return $this->sqlArr;
    }

    /**
     * set sql array
     *
     */
    public function setSqlArr($sql)
    {
        $sqlArr = $this->sqlArr;
        $sqlArr[] = $sql;
        $this->sqlArr = $sqlArr;
    }
}
