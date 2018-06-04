<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-6-4
 * Time: 下午2:19
 */

namespace SWBT\Worker;



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
    public function handleJob()
    {
        $this->logger->info('chenbo',['data'=>$this->job->getData()]);
        return ['code'=>Code::$success];
    }
}