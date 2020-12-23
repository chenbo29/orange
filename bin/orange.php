<?php

use Orange\Orange;

if (PHP_OS !== 'Linux') exit('Only Linux system is supported');
if (php_sapi_name() != 'cli') exit('Please run in command line mode');
if (!function_exists('posix_kill')) exit('ext posix is missing');
if (!function_exists('pcntl_fork')) exit('ext pcntl is missing');

require_once __DIR__ . '/../vendor/autoload.php';
$container = require_once __DIR__ . '/../bootstrap/orange.php';
if ($argc === 1) exit("missing parameter\n");
$daemonize = isset($argv[2]) && ($argv[2] === '-d');
try {
    $orange = new Orange($container, $daemonize);
} catch (Exception $e) {
    exit('new error');
}
switch ($argv[1]) {
    case 'start':
        $orange->run();
        break;
    case 'stop':
        $orange->stop();
        break;
    case 'status':
        $orange->status();
        break;
    default:
        exit('param is not exist');
}