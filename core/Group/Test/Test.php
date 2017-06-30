<?php

namespace Group\Test;

use PHPUnit_Framework_TestCase;

abstract class Test extends PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        if (method_exists($this, '__initialize'))
            $this->__initialize();
    }
}
