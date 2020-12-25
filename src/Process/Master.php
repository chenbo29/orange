<?php

namespace orange\Process;


use orange\Tube;
use Pimple\Container;
use function Composer\Autoload\includeFile;

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

    public function run($infoTube)
    {
        $this->registerSignal();
        if ($this->daemonize) {
            $this->forkMaster($infoTube);
        } else {
            $this->forkWorker();
        }
    }

    public function stop($pid)
    {
//        posix_getpgid()
    }

    public function forkMaster($infoTube)
    {
        $this->infoMaster = [
            'pid'        => 0,
            'name'       => $infoTube['name'],
            'worker_pid' => [],
            'pid_file'   => PID_FILE_TEMPLATE . $infoTube['name'],
            'tube'       => $infoTube,
        ];
        cli_set_process_title(sprintf('orange[%s] master with daemonize', $this->infoMaster['name']));
        file_put_contents($this->infoMaster['pid_file'], posix_getpid());
        $this->infoMaster['pid'] = posix_getpid();
        $this->container['logger']->info(sprintf('orange master start with daemonize'), $this->infoMaster);
        $this->forkWorker();
        $this->handleMaster();
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
            cli_set_process_title(sprintf('orange[%s] worker[%s]', $this->infoMaster['tube']['name'], $identify));
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
            if (posix_kill($this->infoWorker['ppid'], 0)) {
                $this->container['logger']->info($num, $this->infoWorker);
                $num++;
                sleep(1);
            } else {
                $this->container['logger']->info('self killed because of master is dead');
                exit(0);
            }
        } while (true);
    }

    public function handleMaster()
    {
        do {
            pcntl_signal_dispatch();
            $pid = pcntl_wait($status, WUNTRACED);
            if ($pid > 0) {
                unset($this->infoMaster['worker']['pid_' . $pid]);
                $this->container['logger']->info('get pcntl_wait', [$pid, $status, $this->infoMaster]);
                if (empty($this->infoMaster['worker'])) {
                    $this->container['logger']->info('orange master stop', [$this->infoMaster]);
                    @unlink(PID_FILE_TEMPLATE . $this->infoMaster['name']);
                    exit(0);
                }
            } else {
                $this->container['logger']->info(__FUNCTION__, [$pid, $this->infoMaster]);
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
        pcntl_signal(SIGCHLD, [__CLASS__, 'signalHandler'], false);
    }

    /**
     * 信号处理
     * @param $signal
     */
    public function signalHandler($signal)
    {
        $this->container['logger']->info('get signal', [
            'signal' => $signal,
            'pid'    => posix_getpid(),
            'master' => $this->infoMaster,
            'worker' => $this->infoWorker,
        ]);
        switch ($signal) {
            case SIGINT:
                if ($this->infoMaster['pid'] === posix_getpid()) {
                    $this->killWorker(SIGINT);
                } else {
                    exit(0);
                }
                break;
            case SIGUSR2:
                if ($this->isRunning($this->infoMaster['name']) === false) exit(sprintf('orange[%s] is not running', $this->infoMaster['name']));
                $this->container['output']->info(sprintf('The Tube[%s] Status', $this->infoMaster['name']), $this->infoMaster);
                $tube = new Tube($this->container);
                $tube->setName($this->infoMaster['name']);
                $tube->create();
                $this->container['output']->info('status info', $tube->status());
                break;
            case SIGCHLD:
//                $pid = pcntl_wait($status);
//                if ($pid > 0) {
//                    unset($this->infoMaster['worker']['pid_' . $pid]);
//                    $this->container['logger']->info('get pcntl_wait', [$pid, $status, $this->infoMaster]);
//                }
//                if (empty($this->infoMaster['worker'])) {
//                    $this->container['logger']->info('orange master stop', [$this->infoMaster]);
//                    @unlink(PID_FILE_TEMPLATE . $this->infoMaster['name']);
//                    exit(0);
//                }
                //            $status = 0;
//            if ($pid > 0) {
//                $this->container['logger']->error('master listen s worker is exist', [
//                    'pid'    => $pid,
//                    'master' => $this->infoMaster,
//                ]);
//                $this->forkOneWorker(99);
//            }
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

    public function isRunning($name)
    {
        if (file_exists(PID_FILE_TEMPLATE . $name)) {
            $pid = intval(file_get_contents(PID_FILE_TEMPLATE . $name));
            $this->container['output']->info('master is running', [posix_kill($pid, 0) ? '1' : '0', $pid]);
            return posix_kill($pid, 0);
        } else {
            return false;
        }
    }
}