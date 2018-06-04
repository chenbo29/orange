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
    private $loggerFile;
    private $times;

    public function __construct(Container $container, Pheanstalk $beanstalkd, $tube)
    {
        $this->beanstalkd = $beanstalkd;
        $this->logger = $container->logger;
        $this->loggerFile = $container->loggerFile;
        $this->tube = $tube;
        $this->times = 0;
    }

    public function run(){
        while (true){
            $this->reserveJob();
        }
    }

    public function reserveJob(){
        if ($this->times % 10 === 0) $this->logger->info('Reserve Job',['times'=>$this->times,'tube'=>$this->tube]);
        try{
            $this->beanstalkd->watch($this->tube);
            $job = $this->beanstalkd->reserve(getenv('reserveTimeOut'));
            if ($job) {
                $this->logger->info('Reserve Job Data', ['tube'=>$this->tube, 'jodId'=> $job->getId(), 'jobData'=>$job->getData()]);
                $this->loggerFile->info('Reserve Job Data', ['tube'=>$this->tube, 'jodId'=> $job->getId(), 'jobData'=>$job->getData()]);
                $this->beanstalkd->delete($job);
            }
            $this->times++;
        } catch (\Exception $e){
            $this->logger->error($e->getMessage(), ['Exception' => $e]);
        }
        return $job;
    }
}