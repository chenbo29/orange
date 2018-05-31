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
        $tubesProcess->start();
    }

    public function __destruct()
    {
//        echo "\n";
    }
}