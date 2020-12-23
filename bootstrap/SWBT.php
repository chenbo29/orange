<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Symfony\Component\Filesystem\Filesystem;

date_default_timezone_set("Asia/Shanghai");
$rootDir     = dirname(__DIR__) . '/';
define('RUNTIME_PATH', dirname(__DIR__) . '/runtime');
define('PID_FILE', RUNTIME_PATH . '/master_pid');
$configPath  = $rootDir . 'config/SWBT.php';
if (file_exists($configPath)) {
    $container             = new Container(require __DIR__ . '/../config/SWBT.php');
    $container['root_dir'] = $rootDir;
    $container['runtime_path'] = dirname(__DIR__) . '/runtime';
    try {
        $container['logger'] = new Logger('SWBT');
        $container['logger']->pushHandler(new StreamHandler('php://output'));
        // TODO 日志
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