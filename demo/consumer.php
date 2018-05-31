<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-30
 * Time: 下午11:01
 */
require_once __DIR__ . '/../vendor/autoload.php';
$beanstalkd = new \Pheanstalk\Pheanstalk('127.0.0.1');
//var_dump($beanstalkd->stats());
//$job = $beanstalkd->reserveFromTube('test');
//var_dump($job);
//$beanstalkd->delete($job);
//var_dump($beanstalkd->stats());

var_dump($beanstalkd->statsTube('test'));
