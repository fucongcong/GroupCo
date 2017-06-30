<?php

namespace Group\Coroutine;

class Retval
{
    protected $value;
 
    public function __construct($value)
    {
        $this->value = $value;
    }
 
    public function getValue()
    {
        return $this->value;
    }
}
