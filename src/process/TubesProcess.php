<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-29
 * Time: 上午9:45
 */

namespace SWBT\process;


use SWBT\Tubes;

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
        return $tubesProcesses;
    }

    public function startProcess($tubesName){
        $tubes = new Tubes($this->container);
        $processs = new \Swoole\Process(function ($process) use($tubesName, $tubes) {
            $this->logger->info($process->name);
            swoole_timer_tick(1000, function () use($tubesName, $tubes) {
//                $tubes->perform($tubesName);
                echo "parent timer\n";
            });
        });
        $processs->name = "SWBT: tubes $tubesName";
        if ($processs->start()){
            $this->logger->info('process start', ['tubeName' => $tubesName]);
        } else {
            $this->logger->error('process start failed', ['tubeName' => $tubesName]);
        }
        return $processs;
    }

    public function callback(){
        $this->logger->info('process created');
    }
}