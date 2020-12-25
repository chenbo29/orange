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
        $this->container['output'] = new Logger('orange');
        $this->container['output']->pushHandler(new StreamHandler('php://output'));
        if ($this->daemonize) {
            $logDir = LOG_PATH . DIRECTORY_SEPARATOR . date('Ym');
            if (!$container['fileSystem']->exists($logDir)) $container['fileSystem']->mkdir($logDir);
            $this->container['logger']->pushHandler(new StreamHandler($logDir . DIRECTORY_SEPARATOR . date('d') . '.log'));
        } else {
            $this->container['logger']->pushHandler(new StreamHandler('php://output'));
        }
        $this->container['logger'] = $this->container['output'];
    }

    public function run()
    {
        $this->checkEnv();
        $this->container['logger']->info("Orange Get ready to go", $this->container['tubes']);
        try {
            array_walk($this->container['tubes'], function ($tubeInfo, $tubeName) {
                $pid = pcntl_fork();
                if ($pid > 0) {
                    pcntl_wait($status, WUNTRACED);
                } else {
                    $tubeInfo['name'] = $tubeName;
                    $master           = new Master($this->container, $this->daemonize);
                    $master->run($tubeInfo);
                }
            });
        } catch (Exception $e) {
            exit(sprintf("orange of master run with error\n%s", $e->getMessage()));
        }
    }

    public function stop(): void
    {
        $files    = scandir(PID_PATH);
        $pidFiles = array_splice($files, 3);
        array_walk($pidFiles, function ($v) {
            $pid = intval(file_get_contents(PID_PATH . DIRECTORY_SEPARATOR . $v));
            posix_kill($pid, SIGINT);
        });
    }

    public function status()
    {
        $files = scandir(PID_PATH);
        if (count($files) <= 3) exit("orange has no worker is running\n" . count($files));
        $master = new Master($this->container, $this->daemonize);
        array_walk($files, function ($v, $k) use ($master) {
            if ($k > 2 && strpos($v, '_')) {
                $tubeName = explode('_', $v)[1];
                if ($master->isRunning($tubeName)) {
                    $this->container['output']->info('these ', [$tubeName]);
                    $pid = intval(file_get_contents(PID_FILE_TEMPLATE . $tubeName));
                    posix_kill($pid, SIGUSR2);
                    return true;
                } else {
                    $this->container['output']->info('these tube master has not running', [$tubeName]);
                }
            }
        });
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
        if ($this->container['pheanstalk']->getConnection()->isServiceListening() === false) exit("beanstalkd is not availability\n");
    }

    private function checkDirectory()
    {
        if (touch(RUNTIME_PATH . '/test')) {
            unlink(RUNTIME_PATH . '/test');
        } else {
            exit(sprintf("Directory [%s] is not be able to write\n", RUNTIME_PATH));
        }
    }

    private function checkRunning()
    {
        $files = scandir(PID_PATH);
        if (count($files) > 3) {
            exit(sprintf("Orange already running [%s]\n", join(',', array_slice($files, 3))));
        }
    }

    private function checkEnv()
    {
        $this->checkDirectory();
        $this->checkBeanstalkd();
//        $this->checkRunning();
    }
}