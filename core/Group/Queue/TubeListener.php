<?php

namespace Group\Queue;

use Pheanstalk\Pheanstalk;

class TubeListener
{   
    private $jobs;

    private $tubes;

    public function __construct()
    {
        $jobs = \Config::get("queue::queue_jobs");
        $this->setJobs($jobs);
        $this->setTubes();
    }

    /**
     * 设置队列jobs
     *
     * @param jobs 
     */
    public function setJobs($jobs)
    {
        foreach ($jobs as $job) {
            $this->jobs[$job['tube']][$job['job']] = $job;
        }
    }

    /**
     * 设置监听队列
     *
     */
    public function setTubes()
    {
        foreach ($this->jobs as $tube => $job) {
            $task_worker_num = 0;
            foreach ($job as $key => $value) {
               if ($task_worker_num < $value['task_worker_num']) {
                    $task_worker_num = $value['task_worker_num'];
                }
            }
            for($i = 0; $i < $task_worker_num; $i++) {
                $this->tubes[] = $tube;
            }
        }
    }

    /**
     * 获取队列names
     *
     * @return tubes 
     */
    public function getTubes()
    {
        return $this->tubes;
    }

    /**
     * 获取队列jobs
     *
     * @return jobs 
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * 获取队列数量
     *
     * @return int 
     */
    public function getTubesCount()
    {
        return count($this->tubes);
    }

    /**
     * 获取队列数量
     *
     * @param string tube
     * @param Pheanstalk pheanstalk
     * @return string|boolean 
     */
    public function getJob($tube, Pheanstalk $pheanstalk)
    {   
        if (!isset($this->jobs[$tube])) return false;
        
        $timeout = 3;
        $job = $pheanstalk->watch($tube)->reserve($timeout);
        if (empty($job) || !is_object($job) || $job->getId() == 0 || empty($job->getData())) return false;
        
        $data = [
            'job' => $job,
            'handle' => $this->jobs[$tube],
        ];
        return serialize($data);
    }
}