<?php
namespace orange\Process;


use Pimple\Container;

class Master
{
    private $container;
    private $daemonize;
    private $infoMaster;
    private $infoWorker;

    public function __construct(Container $container, $daemonize = false)
    {
        $this->container = $container;
        $this->daemonize = $daemonize;
    }

    public function run()
    {
        $this->registerSignal();
        array_walk($this->container['tubes'], function ($v, $k) {
            $this->infoMaster = [
                'pid'        => 0,
                'name'       => $k,
                'worker_pid' => [],
                'pid_file'   => PID_PATH . DIRECTORY_SEPARATOR . 'pid_' . $k,
                'tube'       => array_merge(['name' => $k], $v),
            ];
            if ($this->daemonize) {
                $this->forkMaster();
            } else {
                $this->forkWorker();
            }
        });
    }

    public function stop($pid)
    {
//        posix_getpgid()
    }


    public function forkMaster()
    {
        $pid = pcntl_fork();
        if ($pid > 0) {
            exit(0);
        } else {
            cli_set_process_title(sprintf('swbt master[%s] with daemonize', $this->infoMaster['name']));
            file_put_contents($this->infoMaster['pid_file'], posix_getpid());
            $this->infoMaster['pid'] = posix_getpid();
            $this->container['logger']->info(sprintf('swbt master start with daemonize'), $this->infoMaster);
            $this->forkWorker();
            $this->handleMaster();
        }
    }

    /**
     * fork worker process for a tube
     */
    public function forkWorker()
    {
        for ($i = 0; $i < $this->infoMaster['tube']['worker_num']; $i++) {
            $this->forkOneWorker($i + 1);
        }
    }

    /**
     * fork one process of worker
     * @param $workerNum
     */
    public function forkOneWorker($workerNum)
    {
        $identify = sprintf('%d-%d', $workerNum, 1);
        $pid      = pcntl_fork();
        if ($pid > 0) {
            $this->infoMaster['worker']['pid_' . $pid] = ['pid' => $pid, 'ppid' => posix_getpid(), 'identify' => $identify];
            $this->container['logger']->info('create a worker process', $this->infoMaster['worker']['pid_' . $pid]);
        } else {
            $this->infoWorker = ['pid' => posix_getpid(), 'ppid' => posix_getppid(), 'identify' => $identify];
            cli_set_process_title(sprintf('swbt worker[%s][%s]', $this->infoMaster['tube']['name'], $workerNum));
            $this->container['logger']->info('worker process is get to do', $this->infoWorker);
            $this->handleWorker();
        }
    }

    /**
     * 处理worker的任务
     */
    public function handleWorker()
    {
        $num = 1;
        do {
            pcntl_signal_dispatch();
            $this->container['logger']->info($num, $this->infoWorker);
            $num++;
            sleep(1);
        } while (true);
    }

    public function handleMaster()
    {
        do {
            pcntl_signal_dispatch();
            $status = 0;
            $pid    = pcntl_wait($status, WUNTRACED);
            if ($pid > 0) {
                $this->container['logger']->error('master listen s worker is exist', [
                    'pid'    => $pid,
                    'master' => $this->infoMaster,
                ]);
                $this->forkOneWorker(99);
            }
        } while (true);
    }

    /**
     * 注册信号
     */
    private function registerSignal()
    {
        pcntl_signal(SIGINT, [__CLASS__, 'signalHandler'], false);
        pcntl_signal(SIGUSR2, [__CLASS__, 'signalHandler'], false);
    }

    /**
     * 信号处理
     * @param $signal
     */
    public function signalHandler($signal)
    {
        $this->container['logger']->info('get signal', [
            'signal' => $signal,
            'worker' => $this->infoWorker,
        ]);
        switch ($signal) {
            case SIGINT:
                if ($this->infoMaster['pid'] === posix_getpid()) {
                    unlink($this->infoMaster['pid_file']);
                    $this->killWorker(SIGINT);
                }
                exit(0);
            case SIGUSR2:
                $this->container['logger']->info('get SIGUSR2 signal ' . $signal);
                break;
            case SIGKILL:
//                todo
                break;
            default:
                $this->container['logger']->info('get signal ' . $signal);
                break;
        }
    }

    /**
     * send signal to a worker
     * @param $signal
     */
    private function killWorker($signal)
    {
        array_walk($this->infoMaster['worker'], function ($v) use ($signal) {
            $this->container['logger']->info('send signal', [
                'pid'    => $v['pid'],
                'signal' => $signal
            ]);
            posix_kill($v['pid'], $signal);
        });
    }
}