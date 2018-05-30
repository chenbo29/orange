<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-30
 * Time: 下午5:15
 */
require_once __DIR__ . '/../vendor/autoload.php';
$beanstalkd = new \Pheanstalk\Pheanstalk('127.0.0.1');
$beanstalkd->useTube('test')->put("chenbo producer" . date('H:i:s -Y-m-d'));