<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-6-4
 * Time: 下午2:29
 */

namespace SWBT\Worker;


use Pheanstalk\Job;
use Pimple\Container;

class BaseWorker
{
    public function __construct(Container $container, Job $job)
    {
        $this->logger = $container['logger'];
        $this->job = $job;
    }

    public function __get($name)
    {
        if (isset($this->$name)){
            return $this->$name;
        } else {
            return null;
        }
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}