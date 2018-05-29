<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-29
 * Time: 上午10:25
 */

namespace SWBT;

class Tubes
{
    private $beanstalkdClient;
    private $logger;

    public function __construct($container)
    {
        $this->beanstalkdClient = $container->pheanstalk;
        $this->logger = $container->logger;
        if (!$this->beanstalkdClient->getConnection()->isServiceListening()){
            $this->logger->info('Connect Listen Beanstalkd False');
        }
    }

    public function perform($tubesName){
        $job = $this->beanstalkdClient->watch($tubesName)->ignore('default')->reserve();
        $jobData = $job->getData();
        $this->logger->info("Job Data: $jobData");
//        chenboTODO 处理相应tubes的任务
        $this->beanstalkdClient->delete($job);
    }
}