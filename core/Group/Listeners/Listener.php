<?php

namespace Group\Listeners;

abstract class Listener
{
    abstract function setMethod();

    public function getMethod()
    {
        return $this->setMethod();
    }
}
