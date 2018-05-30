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
        $this->logger->info('chenbo');
        while (true){
            $this->logger->info('chenbobo');
            $jobData = $this->reserveJob();
//            if (empty($jobData)){
////                continue;
//            }
            $this->logger->info(__FUNCTION__,['jobData' => $jobData]);
        }
    }

    public function reserveJob(){
        try{
            $this->beanstalkd->watch($this->tube);
            $jobData = $this->beanstalkd->reserve();
        } catch (\Exception $e){
            $this->logger->error($e->getMessage(), ['Exception' => $e]);
        }
        if ($jobData) $this->logger->info('Job Data',['data' => (array)$jobData]);
        return $jobData;
    }
}