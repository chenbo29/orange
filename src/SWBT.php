<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-28
 * Time: 下午5:53
 */

namespace SWBT;


use Pimple\Container;

class SWBT
{
    public function __construct(Container $container)
    {
    }

    public function run(){
        echo __FILE__ . __LINE__;
    }

    public function __destruct()
    {
        echo "\n";
    }
}