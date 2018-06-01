<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-29
 * Time: 上午9:45
 */

namespace SWBT\process;


use Pheanstalk\Pheanstalk;
use SWBT\Tubes;
use SWBT\Worker;

class TubesProcess
{
    private $tubes;
    private $container;
    private $logger;
    public function __construct($tubes, $container)
    {
        $this->tubes = $tubes;
        $this->container = $container;
        $this->logger = $container->logger;
    }

    public function start(){
        $tubesProcesses = [];
        foreach ($this->tubes as $tubeName => $tubeInfo){
            $this->logger->info("Tube starting ...", ['tubeName' => $tubeName]);
            $process = $this->startProcess($tubeName);
            array_push($tubesProcesses, $process);
        }
        $this->registerSignal();
        return $tubesProcesses;
    }

    public function startProcess($tubeName){
        $workerProcess = new \Swoole\Process(function ($process) use($tubeName) {
            swoole_set_process_name("SWBT $tubeName tube");
            $tubeWorker = new Worker($this->container, new Pheanstalk('127.0.0.1'), $tubeName);
            $tubeWorker->run();
        });
        if (!$workerProcess->start()) $this->logger->error('Process Start Failed', ['tubeName' => $tubeName, 'swoole_errno'=>swoole_errno, 'swoole_strerror' => swoole_strerror]);
        return $workerProcess;
    }

    private function registerSignal()
    {
        \Swoole\Process::signal(SIGCHLD, function () {
            while ($ret = \Swoole\Process::wait(false)) {
                $this->logger->info("Worker Process Closed", ['Pid'=>$ret['pid']]);
            }
        });
    }
}