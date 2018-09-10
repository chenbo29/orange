<?php
/**
 * Created by PhpStorm.
 * User: kuozhi
 * Date: 2018/9/7
 * Time: 下午12:02
 */

$files = [
    dirname(__DIR__) . '/vendor/autoload.php',
    dirname(__DIR__) . '/autoload.php'
];
require_once  dirname(__DIR__) . '/vendor/autoload.php';
foreach ($files as $file){
    if (is_file($file)){
        require_once $file;
    }
}
