<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-28
 * Time: 下午5:53
 */

namespace SWBT;


use Pimple\Container;
use SWBT\process\TubesProcess;

class SWBT
{
    private $container;
    private $tubes;
    private $logger;
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->logger = $container->logger;
        $config = require __DIR__ . '/../config/SWBT.php';
        $this->tubes = $config['tubes'];
    }

    public function run(){
        swoole_set_process_name('SWBT master');
        $this->logger->info('SWBT Start');
        $tubesProcess = new TubesProcess($this->tubes, $this->container);
        $workerProcesses = $tubesProcess->start();

        foreach ($workerProcesses as $process){
            $this->logger->info('Add Swoole Event',['Pid'=>$process->pid]);
            $this->swooleEvent($process);
        }
        foreach ($workerProcesses as $process){
            $this->logger->info('Start Write To Process',['Pid'=>$process->pid]);
            try{
                $this->writeToProcess($process);
            } catch (\Exception $e){
                $this->logger->info($e->getMessage());
            }
            $this->logger->info('End Write To Process',['Pid'=>$process->pid]);
        }
    }

    private function swooleEvent($process){
//        todo EventLoop暂无概念
        $logger = $this->logger;
        swoole_event_add($process->pipe, function($pipe) use($process, $logger) {
            $info = fread($pipe, 8192);
//            $info = PHP_EOL .' Master  you  are  read from pid =' . $process->pid.' and data = ' . $process->read . PHP_EOL ;
            $logger->info($info);
        },function ($pipe) use ($process, $logger) {
            $info = PHP_EOL . ' Master write  to  pipe ' . $process->pipe .'and data is ' . PHP_EOL;
            $logger->info($info);
            swoole_event_del($pipe);
        });
    }

    private function writeToProcess($process){
        $data = "hello worker[$process->pid]";
        swoole_event_write($process->pipe, $data);
    }

    public function __destruct()
    {
//        echo "\n";
    }
}