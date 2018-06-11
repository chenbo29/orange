<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-6-6
 * Time: 上午11:30
 */

namespace SWBT\Process;


use Pimple\Container;

class Master
{
    private static $pid;
    private $logger;
    private $container;
    public function __construct(Container $container)
    {
        self::$pid = posix_getpid();
        file_put_contents($container['root_dir'] . getenv('masterPidFilePath'), self::$pid);
        $this->container = $container;
        $this->logger = $container['logger'];
    }

    public function run(){
        $tubeProcess = new Tube($this->container);
        $tubeProcess->start();
    }

    public static function isExist(){
        return \Swoole\Process::kill(self::$pid, 0);
    }
}