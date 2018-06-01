<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-6-1
 * Time: 下午5:05
 */
$redirect_stdout = false;
$workers = [];
$worker_num = 1;

for($i = 0; $i < $worker_num; $i++){
    $process = new swoole_process('child_async', $redirect_stdout);
    $pid = $process->start();
    $workers[$pid] = $process;
}

//主进程
foreach($workers as $pid => $process){
    echo PHP_EOL . ' maste start ';
    $process->write("hello worker[$pid]");
    $process->read();
}



function child_async(swoole_process $worker){// 子进程
    swoole_event_add(
        $worker->pipe,  //管道 pipe
        function($pipe) use ($worker) { // 当从管道读取时调用
            echo PHP_EOL.' 当从管道读取时调用 Master  you  are  read from pid ='.$worker->pid.'  and  pipe = '.$pipe  .PHP_EOL ;
        } ,
        function($pipe) use ($worker) { //  当向管道写入时调用
            echo PHP_EOL. ' 当向管道写入时调用 Master write  to  pipe ' . $pipe .'and data is '.$worker->read() .PHP_EOL;
        });
}