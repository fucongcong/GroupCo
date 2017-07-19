<?php

namespace Group\ASync\Pool;

class Result
{
    protected $res;
    
    protected $affectedRows;

    protected $insertId;

    public function __construct($res, $affectedRows, $insertId)
    {
        $this->res = $res;
        $this->affectedRows = $affectedRows;
        $this->insertId = $insertId;
    }

    public function getResult()
    {
        return $this->res;
    }

    public function getAffectedRows()
    {
        return $this->affectedRows;
    }

    public function getInsertId()
    {
        return $this->insertId;
    }
}

