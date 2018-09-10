<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-30
 * Time: 下午4:32
 */

namespace SWBT;


use Pheanstalk\Job;
use Pimple\Container;
use SWBT\Process\Master;

final class Worker
{
    private $container;
    private $beanstalkd;
    private $tube;
    private $pid;
    private $logger;
    private $times;
    private $process;

    public function __construct(Container $container, $process)
    {
        $this->container = $container;
        $this->beanstalkd = $container['pheanstalk'];
        $this->logger = $container['logger'];
        $this->tube = $process['tube'];
        $this->pid = $process['pid'];
        $this->times = 0;
        $this->process = $process;
    }

    public function run():void {
        $this->beanstalkd->watch($this->process['tube']);
        while (true){
            if (!Master::isExist()) {
                $this->process['process']->exit();
            }
            $this->reserveJob();
        }
    }

    public function reserveJob():void {
        $jobInfo = ['tube'=>$this->process['tube'], 'pid' => $this->process['pid']];
        if ($this->times % 100 === 0) $this->logger->info('Reserve Job', array_merge($jobInfo,['times'=>$this->times]));
        $job = $this->beanstalkd->reserve($this->container['beanstalkd']['reserve_timeout']);
        if ($job) {
            $this->logger->info('Reserve Job With Data', array_merge($jobInfo, ['id'=> $job->getId(), 'data'=>$job->getData()]));
            $stClass = $this->container['tubes'][$this->process['tube']]['class'];
            $worker = new $stClass($this->container, $job);
            $this->handleHandleJobResult($worker->handleJob(), $job);
        }
        $this->times++;
    }

    private function handleHandleJobResult(array $result,Job $job): void {
        $buryPriority = !empty($result['buryPriority']) ?: 1025;
        $priority = !empty($result['priority']) ?: 1024;
        $delay = !empty($result['delay']) ?: 6;
        $jobInfo = ['tube' => $this->process['tube'], 'pid' => $this->process['pid'],'id'=>$job->getId(),'data'=>$job->getData()];
        switch ($result['code']){
            case Code::$success:
                $this->beanstalkd->delete($job);
                $this->logger->info('Reserved Job And Delete',$jobInfo);
                break;
            case Code::$delayed:
                $this->beanstalkd->release($job,$priority, $delay);
                $this->logger->info('Reserved Job And Released With Delay',array_merge($jobInfo,['priority'=>$priority, 'delay' => $delay]));
                break;
            case Code::$buried:
                $this->beanstalkd->bury($job, $buryPriority);
                $this->logger->info('Reserved Job And Buried',array_merge($jobInfo,['bury' => $buryPriority]));
                break;
            default:
                $this->logger->error('Function handleJob return error',['result'=>$result,'job'=>[$job->getId(),$job->getData()]]);
                break;
        }
    }
}