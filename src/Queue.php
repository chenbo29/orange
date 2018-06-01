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
        $stats = $this->pheanstalk->stats();
        $stats = $this->handleStats($stats);
        $this->logger->info('Status Info:', $stats['basic']);
        foreach ($stats['detail'] as $key => $value){
            $this->logger->info('Status Info:',[$key=>$value]);
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


}