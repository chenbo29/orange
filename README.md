# SWBT

[![Github Releases](https://img.shields.io/github/downloads/ywna/swbt/latest/total.svg)](https://github.com/YWNA/SWBT)
[![Packagist](https://img.shields.io/packagist/dt/ywna/swbt.svg)](https://packagist.org/packages/ywna/swbt)
![GitHub commits](https://img.shields.io/github/commits-since/ywna/swbt/latest.svg)
[![GitHub release](https://img.shields.io/github/release/ywna/swbt.svg)](https://github.com/YWNA/SWBT/releases)


A PHP Framework of [swoole](https://www.swoole.com/) with [beanstalkd](http://kr.github.io/beanstalkd/)
### Install [beanstalkd](https://github.com/kr/beanstalkd)
* Beanstalk is a simple, fast work queue. http://kr.github.io/beanstalkd/ 
* [Doc](https://github.com/kr/beanstalkd/blob/master/doc/protocol.zh-CN.md)
* Ubuntu,Install Command
    ```
    sudo apt-get install beanstalkd
    ```
    [更多方式](http://kr.github.io/beanstalkd/download.html)

### Install [Swoole](http://www.swoole.com)
* [Doc](https://wiki.swoole.com/wiki/page/6.html)

### Install SWBT
* Composer
    ```
    composer require ywna/swbt
    ```    
### Start-Up（作为第三方依赖）
* 消息队列的状态
    ```bash
    vendor/bin/SWBT status
    ```
    ```log
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"hostname":"chenbo-Vostro-3559","id":"c53887df34bcd127","job-timeouts":"0","pid":"1113","total-connections":"3","total-jobs":"0","uptime":"31447","version":"1.9"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"current-jobs-urgent":"15","des":"优先级小于1024状态为ready的job数量"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"current-jobs-ready":"39","des":"状态为ready的job数量"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"current-jobs-reserved":"0","des":"状态为reserved的job数量"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"current-jobs-delayed":"0","des":"状态为delayed的job数量"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"current-jobs-buried":"2","des":"状态为buried的job数量"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-put":"0","des":"总共执行put指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-peek":"0","des":"总共执行peek指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-peek-ready":"0","des":"总共执行peek-ready指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-peek-delayed":"0","des":"总共执行peek-delayed指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-peek-buried":"0","des":"总共执行peek-buried指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-reserve":"0","des":"总共执行reserve指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-reserve-with-timeout":"0","des":"总共执行reserve-with-timeout指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-delete":"0","des":"总共执行delete指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-release":"0","des":"总共执行release指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-use":"0","des":"总共执行use指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-watch":"0","des":"总共执行watch指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-ignore":"0","des":"总共执行ignore指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-bury":"0","des":"总共执行bury指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-kick":"0","des":"总共执行kick指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-touch":"0","des":"总共执行touch指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-stats":"3","des":"总共执行stats指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-stats-job":"0","des":"总共执行stats-job指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-stats-tube":"0","des":"总共执行stats-tube指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-list-tubes":"0","des":"总共执行list-tubes指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-list-tube-used":"0","des":"总共执行list-tube-used指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-list-tubes-watched":"0","des":"总共执行list-tubes-watched指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"cmd-pause-tube":"0","des":"总共执行pause-tube指令的次数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"max-job-size":"5242880","des":"job的数据部分最大长度"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"current-tubes":"5","des":"当前存在的tube数量"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"current-connections":"1","des":"当前打开的连接数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"current-producers":"0","des":"当前所有的打开的连接中至少执行一次put指令的连接数量"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"current-workers":"0","des":"当前所有的打开的连接中至少执行一次reserve指令的连接数量"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"current-waiting":"0","des":"当前所有的打开的连接中执行reserve指令但是未响应的连接数量"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"rusage-utime":"0.000000","des":"进程总共占用的用户CPU时间"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"rusage-stime":"0.003083","des":"进程总共占用的系统CPU时间"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"binlog-oldest-index":"118","des":"开始储存jobs的binlog索引号"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"binlog-current-index":"123","des":"当前储存jobs的binlog索引号"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"binlog-records-migrated":"0","des":"is the cumulative number of records written as part of compaction."} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"binlog-records-written":"0","des":"binlog累积写入的记录数"} []
    [2018-06-25 17:46:04] SWBT.INFO: Status Info: {"binlog-max-size":"10485760","des":"binlog的最大容量"} []
    ```
* 消息队列的Job状态信息
    ```bash
    vendor/bin/SWBT status-job
    ```
    ```log
    [2018-06-25 17:39:20] SWBT.INFO: Status Info: {"hostname":"chenbo-Vostro-3559","id":"c53887df34bcd127","job-timeouts":"0","pid":"1113","total-connections":"2","total-jobs":"0","uptime":"31043","version":"1.9"} []
    [2018-06-25 17:39:20] SWBT.INFO: Status Info: {"current-jobs-urgent":"15","des":"优先级小于1024状态为ready的job数量"} []
    [2018-06-25 17:39:20] SWBT.INFO: Status Info: {"current-jobs-ready":"39","des":"状态为ready的job数量"} []
    [2018-06-25 17:39:20] SWBT.INFO: Status Info: {"current-jobs-reserved":"0","des":"状态为reserved的job数量"} []
    [2018-06-25 17:39:20] SWBT.INFO: Status Info: {"current-jobs-delayed":"0","des":"状态为delayed的job数量"} []
    [2018-06-25 17:39:20] SWBT.INFO: Status Info: {"current-jobs-buried":"2","des":"状态为buried的job数量"} []
    ```
* bash端方式
    ```
    vendor/bin/SWBT run
    ```
* 守护进程deamon方式    
    ```
    vendor/bin/SWBT start
    ```
* 停止
    ```
    vendor/bin/SWBT stop
    ```
* 队列管道配置
    ```
    swb/config/SWBT.php

    return [
        'tubes' => [
            //队列处理管道名称
            'test' => [
                'worker_num' => 3, //处理进程数量
                'class' => \SWBT\Worker\TestWorker::class //队列处理类
            ]
        ]
    ];
    ```
* 队列处理类模板，继承BaseWorker类和Worker接口
    ```
        <?php
        
        namespace Monkey\Worker;

        use SWBT\Code;
        use SWBT\Worker\BaseWorker;
        use SWBT\Worker\Worker;

        class TestWorker extends BaseWorker implements Worker
        {
            public function handleJob()
            {
                echo 'do something';
                return ['code'=>Code::$success];
                // return ['code'=>Code::$delayed];
                // return ['code'=>Code::$buried];
            }
        }
    ```
### Start-Up（独立项目运行）
* bash端方式
    ```
    bin/SWBT run
    ```
* 守护进程deamon方式    
    ```
    bin/SWBT start
    ```
* 停止
    ```
    bin/SWBT stop
    ```
* 队列管道配置（同上）
* 队列处理类：src/Worker
* 目录storage可读写
