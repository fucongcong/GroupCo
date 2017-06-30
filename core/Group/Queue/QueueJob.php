<?php

namespace Group\Queue;

abstract class QueueJob
{   
    protected $jobId;

    protected $jobData;

    public function __construct($jobId, $jobData)
    {   
        $this->jobId = $jobId;

        $this->jobData = $jobData;
    }

    abstract function handle();
}