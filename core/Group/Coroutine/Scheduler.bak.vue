<?php

namespace Group\Coroutine;

class Scheduler
{
    protected $maxTaskId = 0;
    protected $taskQueue;

    public function __construct()
    {
        $this->taskQueue = new \SplQueue();
    }

    public function newTask(\Generator $coroutine)
    {
        if ($this->maxTaskId >= PHP_INT_MAX) {
            $this->maxTaskId = 0;
        }
        $taskId = ++$this->maxTaskId;
        $task = new Task($taskId, $coroutine);
        $this->taskQueue->enqueue($task);
    }

    public function schedule(Task $task)
    {
        $this->taskQueue->enqueue($task);
    }

    public function run()
    {
        while (!$this->taskQueue->isEmpty()) {
            $task = $this->taskQueue->dequeue();
            $task->run();
        }
    }
}