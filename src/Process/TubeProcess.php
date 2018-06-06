<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-29
 * Time: 上午9:45
 */

namespace SWBT\Process;


use Pimple\Container;
use SWBT\Worker;

class TubeProcess
{
    private $tubes;
    private $container;
    private $logger;
    private $beanstalkd;
    public $processInfo;
    public $processInfoWithPidKey;
    public $processInfoWithTube;
    public function __construct(Container $container)
    {
        $this->tubes = $container['tubes'];
        $this->container = $container;
        $this->logger = $container['logger'];
        $this->beanstalkd = $container->pheanstalk;
    }

    public function start(){
        foreach ($this->tubes as $tube => $tubeInfo){
            $workerNum = ($tubeInfo['worker_num'] > 0) ? $tubeInfo['worker_num'] : 1;
            $this->logger->info("Tube Starting ...", ['tube' => $tube]);
            for ($i = 0; $i < $workerNum; $i++){
                $processInfo = $this->startProcess($tube);
                $this->processInfo[] = $processInfo;
                $this->processInfoWithPidKey[$processInfo['pid']] = $processInfo;
            }
            $this->logger->info("Tube Start Success", ['tube' => $tube, 'pid' => array_keys($this->processInfoWithPidKey)]);
        }
        $this->registerSignal();
        return ;
    }

    private function startProcess($tubeName){
        $processInfo['tube'] = $tubeName;
        $workerProcess = new \Swoole\Process(function ($process) use($processInfo) {
            swoole_set_process_name("SWBT {$processInfo['tube']} tube");
            $tubeWorker = new Worker($this->container, $this->beanstalkd, ['pid'=>$process->pid,'tube'=>$processInfo['tube'],'process'=>$process]);
            $tubeWorker->run();
        });
        if (!$workerProcess->start()) $this->logger->error('Process Start Failed', ['tube' => $processInfo['tube'], 'swoole_errno'=>swoole_errno, 'swoole_strerror' => swoole_strerror]);
        $processInfo['pid'] = $workerProcess->pid;
        return $processInfo;
    }

    private function registerSignal()
    {
        $tubeProcesses = $this->processInfoWithPidKey;
        \Swoole\Process::signal(SIGCHLD, function () use ($tubeProcesses) {
            while ($ret = \Swoole\Process::wait(false)) {
                $this->logger->info("Worker Process Closed", ['tube'=>$tubeProcesses[$ret['pid']]['tube'], 'pid'=>$ret['pid']]);
            }
            $this->logger->info('Stoped');
//            chenboTODO 未理解
            swoole_event_exit();
        });

        \Swoole\Process::signal(SIGTERM, function ($signalNo){
            $this->logger->info('SWBT Is Stoping ....',['signal' => 'SIGTERM', 'signalNo'=>$signalNo]);
        });
        \Swoole\Process::signal(SIGINT, function ($signalNo){
            $this->logger->info('SWBT Is Stoping ....',['signal' => 'SIGINT', 'signalNo'=>$signalNo]);
        });
    }

    private function checkMaster(&$worker){
        $pid = 1;
        if (!\Swoole\Process::kill($pid, 0)){
            $worker->exit();
            $this->logger->info('Worker Process Closed', ['pid' => $worker->pid]);
        }
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        if (isset($this->$name)){
            return $this->$name;
        } else {
            return null;
        }
    }
}