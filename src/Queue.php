<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-6-1
 * Time: 上午11:34
 */

namespace SWBT;


use Pimple\Container;

class Queue
{
    private $pheanstalk;
    private $logger;
    public function __construct(Container $container)
    {
        $this->pheanstalk = $container->pheanstalk;
        $this->logger = $container->logger;
    }

    public function status(){
        $statusFieldDescription = $this->getStatusFieldsDescription();
        $stats = $this->pheanstalk->stats();
        $stats = $this->handleStats($stats);
        $this->logger->info('Status Info:', $stats['basic']);
        foreach ($stats['detail'] as $key => $value){
            $this->logger->info('Status Info:',[$key=>$value, 'des' => $statusFieldDescription[$key]]);
        }
    }

    private function handleStats($stats){
        $basicFields = ['hostname','id','version', 'total-jobs','job-timeouts','total-connections','pid','uptime'];
        $statusBasic = [];
        $statusDetail = [];
        $statusKeys = array_keys((array)$stats);
        foreach ($statusKeys as $key){
            if (in_array($key, $basicFields)){
                $statusBasic[$key] = $stats->{$key};
            } else {
                $statusDetail[$key] = $stats->{$key};
            }
        }
        ksort($statusBasic);
        return ['basic'=>$statusBasic,'detail'=>$statusDetail];
    }

    private function getStatusFieldsDescription(){
        return [
            "current-jobs-urgent" => "优先级小于1024状态为ready的job数量",
            "current-jobs-ready" => "状态为ready的job数量",
            "current-jobs-reserved" => "状态为reserved的job数量",
            "current-jobs-delayed" => "状态为delayed的job数量",
            "current-jobs-buried" => "状态为buried的job数量",
            "cmd-put" => "总共执行put指令的次数",
            "cmd-peek" => "总共执行peek指令的次数",
            "cmd-peek-ready" => "总共执行peek-ready指令的次数",
            "cmd-peek-delayed" => "总共执行peek-delayed指令的次数",
            "cmd-peek-buried" => "总共执行peek-buried指令的次数",
            "cmd-reserve" => "总共执行reserve指令的次数",
            "cmd-use" => "总共执行use指令的次数",
            "cmd-watch" => "总共执行watch指令的次数",
            "cmd-ignore" => "总共执行ignore指令的次数",
            "cmd-release" => "总共执行release指令的次数",
            "cmd-bury" => "总共执行bury指令的次数",
            "cmd-kick" => "总共执行kick指令的次数",
            "cmd-stats" => "总共执行stats指令的次数",
            "cmd-stats-job" => "总共执行stats-job指令的次数",
            "cmd-stats-tube" => "总共执行stats-tube指令的次数",
            "cmd-list-tubes" => "总共执行list-tubes指令的次数",
            "cmd-list-tube-used" => "总共执行list-tube-used指令的次数",
            "cmd-list-butes-watched" => "总共执行list-tubes-watched指令的次数",
            "cmd-pause-tube" => "总共执行pause-tube指令的次数",
            "cmd-list-tubes-watched" => "",
            "cmd-touch" => "",
            "cmd-delete" => "",
            "cmd-reserve-with-timeout" => "",
            "job-timeouts" => "所有超时的job的总共数量",
            "total-jobs" => "创建的所有job数量",
            "max-job-size" => "job的数据部分最大长度",
            "current-tubes" => "当前存在的tube数量",
            "current-connections" => "当前打开的连接数",
            "current-producers" => "当前所有的打开的连接中至少执行一次put指令的连接数量",
            "current-workers" => "当前所有的打开的连接中至少执行一次reserve指令的连接数量",
            "current-waiting" => "当前所有的打开的连接中执行reserve指令但是未响应的连接数量",
            "total-connections" => "总共处理的连接数",
            "pid" => "服务器进程的id",
            "version" => "服务器版本号",
            "rusage-utime" => "进程总共占用的用户CPU时间",
            "rusage-stime" => "进程总共占用的系统CPU时间",
            "uptime" => "服务器进程运行的秒数",
            "binlog-oldest-index" => "开始储存jobs的binlog索引号",
            "binlog-current-index" => "当前储存jobs的binlog索引号",
            "binlog-max-size" => "binlog的最大容量",
            "binlog-records-written" => "binlog累积写入的记录数",
            "binlog-records-migrated" => "is the cumulative number of records written as part of compaction.",
            "id" => "一个随机字符串，在beanstalkd进程启动时产生",
            "hostname" => "主机名",
        ];
    }
}