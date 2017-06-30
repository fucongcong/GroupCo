<?php

namespace Group\Events;

class Event
{	
	protected $property;

    public function __construct($property = null)
    {
        $this->property = $property;
    }

    public function getProperty()
    {
        return $this->property;
    }

    public function setProperty($property)
    {
        $this->property = $property;
    }
}
