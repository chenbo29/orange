<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-6-4
 * Time: 下午2:19
 */

namespace orange\Worker;



use Pheanstalk\Job;
use Pimple\Container;
use SWBT\Code;

class TestWorker extends BaseWorker implements Worker
{
	public function __construct(Container $container, Job $job)
    {
        parent::__construct($container, $job);
    }

    /**
     * 处理job
     */
    public function handleJob():array
    {
        $this->logger->info('job处理成功日志信息输出',['id' =>$this->job->getId(), 'data'=>$this->job->getData()]);
        return ['code'=>Code::$success];
    }
}