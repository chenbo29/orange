<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-28
 * Time: 下午2:18
 */

$container = new \Pimple\Container(require_once __DIR__ . '/../config/SWBT.php');
$logger = new \Monolog\Logger('SWBT');
$loggerFile = new \Monolog\Logger('SWBTFile');
$pheanstalk = new Pheanstalk\Pheanstalk('127.0.0.1');
$dotenv = new \Dotenv\Dotenv(__DIR__.'/../');
$dotenv->load();

$logger->pushHandler(new \Monolog\Handler\StreamHandler('php://output'));
$container->logger = $logger;
$loggerFile->pushHandler(new \Monolog\Handler\StreamHandler(__DIR__ . '/../storage/logs/' . date('Y-m-d') . '.log'));
$container->loggerFile = $loggerFile;
$container->pheanstalk = $pheanstalk;
$container->env = $dotenv;

return $container;