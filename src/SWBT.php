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
        swoole_set_process_name('SWBT master');
    }

    public function run(){
        $this->logger->info('Start SWBT');
        $tubesProcess = new TubesProcess($this->tubes, $this->container);
        $tubesProcess->start();
    }

    public function __destruct()
    {
//        echo "\n";
    }
}