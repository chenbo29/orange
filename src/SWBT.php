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
    private $daemon;
    private $masterPidFilePath;

    /**
     * SWBT constructor.
     * @param Container $container
     * @param false $daemon
     * @throws Exception
     */
    public function __construct(Container $container, $daemon = false)
    {
        $this->container           = $container;
        $this->fileSystem          = $container['fileSystem'];
        $this->daemon              = $daemon;
        $this->masterPidFilePath   = RUNTIME_PATH . $this->container['pid']['file_path'];
        $this->container['logger'] = function () {
            return new Logger($this->container['log']['name']);
        };
        if ($this->daemon) {
            $this->container['logger']->pushHandler(new StreamHandler(RUNTIME_PATH . $this->container['log']['path'] . date('Y-m-d') . '.log'));
        } else {
            $this->container['logger']->pushHandler(new StreamHandler('php://output'));
        }
    }

    /**
     * 启动
     * @param false $daemonize 是否创建守护进程
     * @return bool
     */
    public function run($daemonize = false): bool
    {
        $this->checkEnv();
        $this->container['logger']->info("SWBT Get ready to go");
        try {
            $master = new Master($this->container);
            $master->run($daemonize);
        } catch (Exception $e) {
            $this->container['logger']->error('SWBT of master start failed', [$e->getMessage()]);
            $this->clean();
        }
        return true;
    }

    public function stop(): void
    {
        $pid = $this->getPid();
        if ($pid) {
            $master = new Master($this->container);
            $master->stop($pid);
            $this->container['logger']->info('Stopped', ['pid' => $pid]);
        } else {
            $this->container['logger']->info('swbt is not running', ['pid' => $pid]);
        }
    }

    public function status()
    {
        if ($this->isRunning()) {
            $this->container['logger']->info('swbt is running', ['pid' => $this->getPid()]);
        } else {
            $this->container['logger']->info('swbt is not running');
        }
    }

    /**
     * 主进程运行中
     * @return bool
     */
    private function isRunning(): bool
    {
        if (file_exists(PID_FILE)) {
            $pid = intval(file_get_contents(PID_FILE));
            if ($pid === 0) {
                unlink(PID_FILE);
                return false;
            } else {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取主进程id
     * @return int
     */
    private function getPid(): int
    {
        if ($this->isRunning()) {
            $pid = intval(file_get_contents(PID_FILE));
            if ($pid) return $pid;
        }
        return 0;
    }

    private function clean()
    {
        $this->container['fileSystem']->remove(PID_FILE);
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
        if ($this->isRunning() === true) {
            $this->container['logger']->info("SWBT already running", ['pid' => $this->getPid()]);
            exit(0);
        }
    }

    private function checkEnv()
    {
        $this->checkDirectory();
        $this->checkBeanstalkd();
        $this->checkRunning();
    }
}