### 项目地址
[SWBT框架 https://github.com/YWNA/SWBT](https://github.com/YWNA/SWBT)
### 目的
基于Swoole和beanstalkd实现多进程处理消息队列。
### 安装
```
composer require ywna/swbt
```
### 初始化
```
vendor/bin/SWBT init
```
执行初始化命令后将会自动生成swbt文件夹。

```
swbt
├── config
│   └── SWBT.php
├── .env
└── storage
    ├── logs
    │   ├── 2018-06-12.log
    │   └── 2018-06-19.log
    └── master.pid
```
1. SWBT.php文件用于配置消息队列管道及其处理类
2. .env项目配置文件
3. logs文件夹下为deamon方式运行下产生的日志内容
4. master.pid是运行时的进程PID信息
### 命令
1. 查看beanstalkd的状态信息
    ```
    vendor/bin/SWBT status
    ```
2. 查看beanstalkd的job信息
    ```
    vendor/bin/SWBT status-job
    ```
3. 启动（deamon）
    ```
    vendor/bin/SWBT start
    ```
4. 启动
    ```
    vendor/bin/SWBT run
    ```
5. 停止
    ```
    vendor/bin/SWBT stop
    ```
6. 重启
    ```
    vendor/bin/SWBT restart
    ```
### 其它
1. 子进程异常退出时将会自动重启。

----------
文章内容更新中