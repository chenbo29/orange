<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-28
 * Time: 下午2:18
 */

$container = new \Pimple\Container(require_once __DIR__ . '/../config/SWBT.php');

$loggerFile = new \Monolog\Logger('SWBTFile');
$dotenv = new \Dotenv\Dotenv(__DIR__.'/../');
$dotenv->load();
$pheanstalk = new Pheanstalk\Pheanstalk(getenv('beanstalkdHost'));


$container->pheanstalk = $pheanstalk;
$container->env = $dotenv;

return $container;