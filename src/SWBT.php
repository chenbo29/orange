<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-28
 * Time: 下午5:53
 */

namespace SWBT;


use Pimple\Container;

class SWBT
{
    private $container;
    public function __construct(Container $container)
    {
        $this->container = $container;
        swoole_set_process_name('SWBT master');
    }

    public function run(){
        $process = new \Swoole\Process(function (){
            swoole_timer_after(5000, function () {
                $this->container->logger->info('Hello World'.date('Y-m-d H:i:s', time()),['a'=>'d']);
                $this->container->logger->info('Hello World'.date('Y-m-d H:i:s', time()),['a'=>'d']);
            });
        });
        if (!$process) $this->container->logger->error("create process error", ['swoole_errno'=>swoole_errno, 'swoole_strerror'=>swoole_strerror]);
        $process->name('SWBT child process');
        $process->start();
    }

    public function __destruct()
    {
//        echo "\n";
    }
}