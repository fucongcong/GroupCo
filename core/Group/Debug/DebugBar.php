<?php

namespace Group\Debug;

class DebugBar extends \DebugBar\DebugBar
{
    public function __construct()
    {
        // $this->addCollector(new \DebugBar\DataCollector\PhpInfoCollector());
        // $this->addCollector(new \DebugBar\DataCollector\MessagesCollector());
        // $this->addCollector(new \DebugBar\DataCollector\RequestDataCollector());
        // $this->addCollector(new \DebugBar\DataCollector\TimeDataCollector());
        // $this->addCollector(new \DebugBar\DataCollector\MemoryCollector());
        $this->addCollector(new \Group\Debug\Collector\ServiceCollector());

        // $debugStack = app()->singleton('debugStack', function () {
        //     return  new \Doctrine\DBAL\Logging\DebugStack();
        // });
        // $this->addCollector(new \DebugBar\Bridge\DoctrineCollector($debugStack));
    }
}