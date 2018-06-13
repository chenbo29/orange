# SWBT

[![Github Releases](https://img.shields.io/github/downloads/ywna/swbt/latest/total.svg)](https://github.com/YWNA/SWBT)
[![Packagist](https://img.shields.io/packagist/dt/ywna/swbt.svg)](https://packagist.org/packages/ywna/swbt)
![GitHub commits](https://img.shields.io/github/commits-since/ywna/swbt/latest.svg)
[![GitHub release](https://img.shields.io/github/release/ywna/swbt.svg)](https://github.com/YWNA/SWBT/releases)


A PHP Framework of [swoole](https://www.swoole.com/) with [beanstalkd](http://kr.github.io/beanstalkd/)
### Install [beanstalkd](https://github.com/kr/beanstalkd)
* Beanstalk is a simple, fast work queue. http://kr.github.io/beanstalkd/
* [官方说明文档](https://github.com/kr/beanstalkd/blob/master/doc/protocol.zh-CN.md)
* Ubuntu,Install Command
    ```
    sudo apt-get install beanstalkd
    ```
* Debian,Install Command
    ```
    sudo apt-get install beanstalkd
    ```
* [更多方式](http://kr.github.io/beanstalkd/download.html)

### Install [Swoole](http://www.swoole.com)
* [文档](https://wiki.swoole.com/wiki/page/6.html)

### Install SWBT
* Composer
    ```
    composer require ywna/swbt
    ```
* 文件读写权限
    ```
    目录storage可读写
    ```
    
### Start-Up（作为第三方依赖）
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
* 队列处理类模板
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
