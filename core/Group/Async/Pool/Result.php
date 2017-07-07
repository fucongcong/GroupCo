<?php

namespace Group\ASync\Pool;

class Result
{
    protected $res;
    
    protected $affected_rows;

    protected $insert_id;

    public function __construct($res, $affected_rows, $insert_id)
    {
        $this->res = $res;
        $this->affected_rows = $affected_rows;
        $this->insert_id = $insert_id;
    }

    public function getResult()
    {
        return $this->res;
    }

    public function getAffectedRows()
    {
        return $this->affected_rows;
    }

    public function getInsertId()
    {
        return $this->insert_id;
    }
}

