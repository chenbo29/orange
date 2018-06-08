<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-28
 * Time: 下午2:18
 */
$rootDir = __DIR__ . '/../';
$container = new \Pimple\Container(require_once $rootDir . 'config/SWBT.php');
$logger = new \Monolog\Logger('SWBT');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('php://output'));
$dotenv = new \Dotenv\Dotenv(__DIR__.'/../');
$dotenv->load();
$pheanstalk = new Pheanstalk\Pheanstalk(getenv('beanstalkdHost'));
$container['logger'] = $logger;
$container['root_dir'] = $rootDir;
$container['pheanstalk'] = $pheanstalk;

return $container;