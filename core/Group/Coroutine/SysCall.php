<?php

namespace Group\Coroutine;

class SysCall {

    protected $callback;
 
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }
 
    public function __invoke(Task $task)
    {
        return call_user_func($this->callback, $task);
    }
}