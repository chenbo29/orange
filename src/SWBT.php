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
    private $daemon;
    private $masterPidFilePath;
    public function __construct(Container $container, $daemon = false)
    {
        $this->container           = $container;
        $this->masterPidFilePath   = $container['swbt_dir'] . getenv('masterPidFilePath');
        $this->container['logger'] = function (){
            return new Logger(getenv('log_name'));
        };
        $this->daemon              = $daemon;
        if ($this->daemon){
            $this->container['logger']->pushHandler(new StreamHandler($container['swbt_dir'] . getenv('log_path') . '/' . date('Y-m-d') . '.log'));
            \Swoole\process::daemon();
        } else {
            $this->container['logger']->pushHandler(new StreamHandler('php://output'));
        }
        $this->logger = $this->container['logger'];
    }

    public function run(){
        if ($this->isRunning()){
            echo "SWBT Pid {$this->getPid()} Already Runing\n";
            exit;
        }
        $master = new Master($this->container);
        $master->run();
    }

    public function stop(){
        $pid = $this->getPid();
        exec("kill -9 $pid");
        unlink($this->masterPidFilePath);
        $this->logger->info('Stoped', ['pid' => $pid]);
    }

    public function init(){
        if (!$this->container['is_independent_project']) {
            $swbtPath = $this->container['swbt_dir'];
            copy(dirname(__DIR__) . '/' . $this->container['env_name'], $swbtPath . $this->container['env_name']);
            copy(dirname(__DIR__) . '/config/SWBT.php', $swbtPath . 'config/SWBT.php');
            $paths = [$swbtPath, $swbtPath . 'config', $swbtPath . 'storage', $swbtPath . 'storage/logs'];
            array_walk($paths, function ($path){
                if (!file_exists($path)) {
                    mkdir($path);
                } elseif (!is_writeable($path)) {
                    $this->logger->error('Permission Denied', ['path' => $path]);
                }
            });
        }
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

    private function isRunning(){
        if (file_exists($this->masterPidFilePath)){
            $pid = intval(file_get_contents($this->masterPidFilePath));
            if ($pid && \Swoole\Process::kill($pid, 0)) {
                return true;
            }
        }
        return false;
    }

    private function getPid(){
        if ($this->isRunning()){
            $pid = intval(file_get_contents($this->masterPidFilePath));
            if (\Swoole\Process::kill($pid, 0)) {
                return $pid;
            }
        } else {
            $this->logger->error('SWBT Is Not Running');
            return 0;
        }
    }

//    public function __destruct()
//    {
//    }
}