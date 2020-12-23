<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Symfony\Component\Filesystem\Filesystem;

date_default_timezone_set("Asia/Shanghai");
$rootDir = dirname(__DIR__) . '/';
define('RUNTIME_PATH', dirname(__DIR__) . '/runtime');
define('LOG_PATH', RUNTIME_PATH . '/log');
define('PID_PATH', RUNTIME_PATH . '/pid');
$configPath = $rootDir . 'config/SWBT.php';
if (file_exists($configPath)) {
    $container = new Container(require __DIR__ . '/../config/SWBT.php');
    try {
        $container['pheanstalk'] = new Pheanstalk\Pheanstalk($container['beanstalkd']['host']);
        $container['fileSystem'] = new Filesystem();
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
        exit;
    }
} else {
    die('config file is not exist');
}
return $container;