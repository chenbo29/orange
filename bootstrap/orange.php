<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Pimple\Container;
use Symfony\Component\Filesystem\Filesystem;

date_default_timezone_set("Asia/Shanghai");
$rootDir = dirname(__DIR__) . '/';
define('RUNTIME_PATH', dirname(__DIR__) . '/runtime');
define('LOG_PATH', RUNTIME_PATH . '/log');
define('PID_PATH', RUNTIME_PATH . '/pid');
if (file_exists($rootDir . 'config/config.php')) {
    $container = new Container(require $rootDir . 'config/config.php');
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