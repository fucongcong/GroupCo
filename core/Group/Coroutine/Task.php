<?php

namespace Group\Coroutine;

use Group\Handlers\ExceptionsHandler;

class Task
{   
    public $container;

    protected $taskId;

    protected $coStack;

    protected $coroutine;

    protected $exception = null;

    protected $sendValue = null;

    /**
     * [__construct 构造函数，生成器+taskId, taskId由 scheduler管理]
     * @param Generator $coroutine [description]
     * @param [type]    $task      [description]
     */
    public function __construct($taskId, $container, \Generator $coroutine)
    {
        $this->taskId = $taskId;
        $this->container = $container;
        $this->coroutine = $coroutine;
        $this->coStack = new \SplStack();
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    /**
     * [getTaskId 获取task id]
     * @return [type] [description]
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * [setException  设置异常处理]
     * @param [type] $exception [description]
     */
    public function setException($exception)
    {
        $this->exception = $exception;
    }

    /**
     * [run 协程调度]
     * @param  Generator $coroutine [description]
     * @return [type]         [description]
     */
    public function run()
    {
        while (true) {
            try {
                if ($this->exception) {

                    $this->coroutine->throw($this->exception);
                    $this->exception = null;
                    continue;
                }

                $value = $this->coroutine->current();

                //\Log::info($this->taskId.__METHOD__ . " value === " . print_r($value, true), [__CLASS__]);

                //如果是coroutine，入栈
                if ($value instanceof \Generator) {
                    $this->coStack->push($this->coroutine);
                    $this->coroutine = $value;
                    continue;
                }

                //如果为null，而且栈不为空，出栈
                if (is_null($value) && !$this->coStack->isEmpty()) {
                    $this->coroutine = $this->coStack->pop();
                    $this->coroutine->send($this->sendValue);
                    continue;
                }

                //如果是系统调用
                if ($value instanceof SysCall || is_subclass_of($value, SysCall::class)) {
                    call_user_func($value, $this);
                    return;
                }

                //如果是异步IO
                if ($value instanceof \Group\Async\Client\Base || is_subclass_of($value, \Group\Async\Client\Base::class)) {
                    $this->coStack->push($this->coroutine);
                    $value->call(array($this, 'callback'));
                    return;
                }

                if ($this->coStack->isEmpty()) {
                    return;
                }

                $this->coroutine = $this->coStack->pop();
                $this->coroutine->send($value);
                //\Log::info($this->taskId.__METHOD__ . " values  pop and send", [__CLASS__]);

            } catch (\Exception $e) {
                $exception = new ExceptionsHandler($this->container);
                $exception->handleException($e);
                if ($this->coStack->isEmpty()) {
                    return;
                }
            }
        }
    }

    public function callback($response, $error, $calltime)
    {
        $this->coroutine = $this->coStack->pop();
        $callbackData = array('response' => $response, 'error' => $error, 'calltime' => $calltime);
        $this->send($callbackData);
        $this->run();
    }

    public function send($sendValue) {
        $this->sendValue = $sendValue;
        return $this->coroutine->send($sendValue);
    }

    public function isFinished()
    {
        return !$this->coroutine->valid();
    }

    public function getCoroutine()
    {
        return $this->coroutine;
    }
}
