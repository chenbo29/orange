<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-30
 * Time: 下午4:32
 */

namespace SWBT;


use Pheanstalk\Pheanstalk;
use Pimple\Container;

class Worker
{
    private $beanstalkd;
    private $tube;
    private $logger;

    public function __construct(Container $container, Pheanstalk $beanstalkd, $tube)
    {
        $this->beanstalkd = $beanstalkd;
        $this->logger = $container->logger;
        $this->tube = $tube;
    }

    public function run(){
        while (true){
            $this->reserveJob();
        }
    }

    public function reserveJob(){
        try{
            $this->beanstalkd->watch($this->tube);
            $job = $this->beanstalkd->reserve();
            if ($job) {
                $this->logger->info('Reserve Job Data', ['tube'=>$this->tube, 'jodId'=> $job->getId(), 'jobData'=>$job->getData()]);
                $this->container->loggerFile->info('Reserve Job Data', ['tube'=>$this->tube, 'jodId'=> $job->getId(), 'jobData'=>$job->getData()]);
                $this->beanstalkd->delete($job);
            }
        } catch (\Exception $e){
            $this->logger->error($e->getMessage(), ['Exception' => $e]);
        }
        return $job;
    }
}