<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-28
 * Time: 下午2:18
 */

$container = new \Pimple\Container(require_once __DIR__ . '/../config/SWBT.php');
$logger = new \Monolog\Logger('SWBT');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('php://output'));
$container->logger = $logger;

$pheanstalk = new Pheanstalk\Pheanstalk('127.0.0.1');
$container->pheanstalk = $pheanstalk;
return $container;