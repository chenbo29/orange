<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-29
 * Time: 上午9:01
 */
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

swoole_timer_tick(1000, function () {
    echo "parent timer\n";
});

function callback_function () {
    swoole_timer_after(10000, function () {
        echo "hello world";
    });
    global $redis;
};


swoole_process::signal(SIGCHLD, function ($sig) {
    echo "sig:{$sig}\n";
    //回收结束运行的子进程。
    while ($ret = Swoole\Process::wait(false)) {
        // create a new child process
        $p = new Swoole\Process('callback_function');
        $p->start();
    }
});

// create a new child process
$p = new Swoole\Process('callback_function');

swoole_event_add($p->pipe, function ($pipe) use ($p) {
    echo "swoole_event_add";
    echo $p->read();
});

$p->start();