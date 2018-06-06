# SWBT
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
    composer install ywna/swbt
    ```
* 文件读写权限
    ```
    目录storage可读写
    ```
    
### Start-Up
* bash端方式
    ```
    bin/SWBT run
    ```
* 守护进程deamon方式    
    ```
    bin/SWBT start
    //The log path 'storage/logs'
    ```
* 停止
    ```
    bin/SWBT stop
    ```

