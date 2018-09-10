<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-28
 * Time: 下午2:18
 */
date_default_timezone_set("Asia/Shanghai");
$rootDir = __DIR__ . '/../';
if (file_exists($rootDir . '/vendor')){
    $swbtDir = $rootDir;
    $isIndependentProject = true;
} else {
    $rootDir .= '../../../';
    $swbtDir = $rootDir . 'swbt/';
    if (!file_exists($rootDir)){
        echo "Run vendor/bin/SWBT init\n";
        exit;
    }
    $isIndependentProject = false;
}
if (file_exists($swbtDir . 'config/SWBT.php')){
    $container = new \Pimple\Container(require_once $swbtDir . 'config/SWBT.php');
    try {
        $logger = new \Monolog\Logger('SWBT');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://output'));
        $pheanstalk = new Pheanstalk\Pheanstalk($container['beanstalkd']['host']);
    } catch (Exception $e){
        echo $e->getMessage() . "\n";
        exit;
    }
    $container['logger'] = $logger;
    $container['pheanstalk'] = $pheanstalk;
} else {
    $container = new \Pimple\Container();
}
$container['root_dir'] = $rootDir;
$container['swbt_dir'] = $swbtDir;
$container['is_independent_project'] = $isIndependentProject;

return $container;