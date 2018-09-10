<?php
/**
 * Created by PhpStorm.
 * User: kuozhi
 * Date: 2018/9/10
 * Time: 下午1:04
 */

namespace SWBT\Composer\Script;

use Composer\Script\Event;

class ScriptHandle
{
    public static function postPackageInstall(Event $event){
        $installedPackage = $event->getName();
        mkdir($installedPackage . date('H-i-s', time()));
    }
}