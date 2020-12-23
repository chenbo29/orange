<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-28
 * Time: 下午5:53
 */

namespace SWBT;


use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use SWBT\Process\Master;

final class SWBT
{
    private $container;
    private $fileSystem;
    private $daemonize;
    private $masterPidFilePath;

    /**
     * SWBT constructor.
     * @param Container $container
     * @param false $daemonize
     * @throws Exception
     */
    public function __construct(Container $container, $daemonize = false)
    {
        $this->container           = $container;
        $this->fileSystem          = $container['fileSystem'];
        $this->daemonize           = $daemonize;
        $this->masterPidFilePath   = RUNTIME_PATH . $this->container['pid']['file_path'];
        $this->container['logger'] = function () {
            return new Logger($this->container['log']['name']);
        };
        if ($this->daemonize) {
            $logDir = LOG_PATH . DIRECTORY_SEPARATOR . date('Y-m');
            if (!file_exists($logDir)) $container['fileSystem']->mkdir($logDir);
            $this->container['logger']->pushHandler(new StreamHandler($logDir . DIRECTORY_SEPARATOR . date('d') . '.log'));
        } else {
            $this->container['logger']->pushHandler(new StreamHandler('php://output'));
        }
    }

    public function run()
    {
        $this->checkEnv();
        $this->container['logger']->info("SWBT Get ready to go");
        try {
            $master = new Master($this->container, $this->daemonize);
            $master->run();
        } catch (Exception $e) {
            exit('SWBT of master start failed');
        }
    }

    public function stop(): void
    {
        //todo
    }

    public function status()
    {
        //todo
    }

    private function clean()
    {
        $files = scandir(PID_PATH);
        array_walk($files, function ($v) {
            if (!in_array($v, ['.', '..', '.gitignore'])) $this->container['fileSystem']->remove($v);
        });
    }

    private function checkBeanstalkd()
    {
        if ($this->container['pheanstalk']->getConnection()->isServiceListening() === false) {
            $this->container['logger']->error("beanstalkd is not availability", $this->container['beanstalkd']);
            exit(0);
        }
    }

    private function checkDirectory()
    {
        if (touch(RUNTIME_PATH . '/test')) {
            unlink(RUNTIME_PATH . '/test');
        } else {
            $this->container['logger']->error(sprintf('Dir [%s] is not be able to write', RUNTIME_PATH));
            exit(0);
        }
    }

    private function checkRunning()
    {
        $files = scandir(PID_PATH);
        if (count($files) > 3) {
            exit(sprintf('SWBT already running [%s]', join(',', array_slice($files, 3))));
        }
    }

    private function checkEnv()
    {
        $this->checkDirectory();
        $this->checkBeanstalkd();
        $this->checkRunning();
    }
}