<?php

use SWBT\SWBT;

if (PHP_OS !== 'Linux') exit('Only Linux system is supported');
if (php_sapi_name() != 'cli') exit('Please run in command line mode');
if (!function_exists('posix_kill')) exit('ext posix is missing');
if (!function_exists('pcntl_fork')) exit('ext pcntl is missing');

require_once __DIR__ . '/../vendor/autoload.php';
$container = require_once __DIR__ . '/../bootstrap/SWBT.php';
$container['process'] = [];
$swbt      = new SWBT($container);
if ($argc === 1) exit("missing parameter\n");
switch ($argv[1]) {
    case 'start':
        $swbt->run(isset($argv[2]) && ($argv[2] === '-d'));
        break;
    case 'stop':
        $swbt->stop();
        break;
    case 'status':
        $swbt->status();
        break;
    default:
        $container['logger']->error('parameter is not exist', $argv);
}