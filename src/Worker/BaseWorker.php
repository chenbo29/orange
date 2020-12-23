<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-6-4
 * Time: 下午2:29
 */

namespace orange\Worker;


use Pheanstalk\Job;
use Pimple\Container;

class BaseWorker
{
	protected $logger;
	protected $job;

	public function __construct(Container $container, Job $job)
    {
        $this->logger = $container['logger'];
        $this->job = $job;
    }
}