<?php
namespace orange;


use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use orange\Process\Master;

final class Orange
{
    private $container;
    private $daemonize;

    /**
     * Orange constructor.
     * @param Container $container
     * @param false $daemonize
     * @throws Exception
     */
    public function __construct(Container $container, $daemonize = false)
    {
        $this->container           = $container;
        $this->daemonize           = $daemonize;
        $this->container['logger'] = new Logger($this->container['log']['name']);
        if ($this->daemonize) {
            $logDir = LOG_PATH . DIRECTORY_SEPARATOR . date('Ym');
            if (!$container['fileSystem']->exists($logDir)) $container['fileSystem']->mkdir($logDir);
            $this->container['logger']->pushHandler(new StreamHandler($logDir . DIRECTORY_SEPARATOR . date('d') . '.log'));
        } else {
            $this->container['logger']->pushHandler(new StreamHandler('php://output'));
        }
    }

    public function run()
    {
        $this->checkEnv();
        $this->container['logger']->info("Orange Get ready to go");
        try {
            $master = new Master($this->container, $this->daemonize);
            $master->run();
        } catch (Exception $e) {
            exit('Orange of master start failed');
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
            exit(sprintf('Orange already running [%s]', join(',', array_slice($files, 3))));
        }
    }

    private function checkEnv()
    {
        $this->checkDirectory();
        $this->checkBeanstalkd();
        $this->checkRunning();
    }
}