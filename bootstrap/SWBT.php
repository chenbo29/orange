<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-28
 * Time: 下午2:18
 */

$container = new \Pimple\Container(require_once __DIR__ . '/../config/SWBT.php');
$container['root_dir'] = __DIR__ . '/../';
$logger = new \Monolog\Logger('SWBT');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('php://output'));
$dotenv = new \Dotenv\Dotenv(__DIR__.'/../');
$dotenv->load();
$pheanstalk = new Pheanstalk\Pheanstalk(getenv('beanstalkdHost'));


$container->pheanstalk = $pheanstalk;
$container->env = $dotenv;
$container['logger'] = $logger;

return $container;