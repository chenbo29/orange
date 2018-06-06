<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-28
 * Time: 下午5:53
 */

namespace SWBT;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use SWBT\Process\Master;

class SWBT
{
    private $container;
    private $logger;
    private $deamon;
    public function __construct(Container $container, $deamon)
    {
        $this->container = $container;
        $this->logger = new Logger('SWBT');
        $this->container['logger'] = function ($c){
            return new Logger($c['loggerName']);
        };
        $this->deamon = $deamon;
        if ($this->deamon){
            $this->container['logger']->pushHandler(new StreamHandler(__DIR__ . '/../storage/logs/' . date('Y-m-d') . '.log'));
            \Swoole\process::daemon();
        } else {
            $this->container['logger']->pushHandler(new StreamHandler('php://output'));
        }
        $this->logger = $this->container['logger'];
    }

    public function run(){
        swoole_set_process_name('SWBT master');
        $this->logger->info('SWBT Start',['pid' => posix_getpid()]);
        $master = new Master($this->container);
        $master->run();
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