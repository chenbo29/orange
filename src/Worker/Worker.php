<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-6-4
 * Time: 下午2:18
 */

namespace Orange\Worker;


use Pheanstalk\Job;
use Pimple\Container;

interface Worker
{
    public function __construct(Container $container, Job $job);

    public function handleJob();
}