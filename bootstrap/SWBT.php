<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-28
 * Time: 下午2:18
 */
$rootDir = __DIR__ . '/../';
$container = new \Pimple\Container(require_once $rootDir . 'config/SWBT.php');
if (file_exists($rootDir . '/vendor')){
    $container['root_dir'] = $rootDir;
    $container['env_name'] = '.env';
    $container['is_independent_project'] = true;
} else {
    $container['root_dir'] = $rootDir . '/../../';
    $container['env_name'] = 'swbt.env';
    $container['is_independent_project'] = false;
}
try {
    $logger = new \Monolog\Logger('SWBT');
    $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://output'));
    $dotenv = new \Dotenv\Dotenv($container['root_dir'], $container['env_name']);
    $dotenv->load();
    $pheanstalk = new Pheanstalk\Pheanstalk(getenv('beanstalkdHost'));
} catch (Exception $e){
    echo $e->getMessage() . "\n";
    exit;
}
$container['logger'] = $logger;
$container['pheanstalk'] = $pheanstalk;

return $container;