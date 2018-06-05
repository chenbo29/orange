<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-6-4
 * Time: 下午3:15
 */

namespace SWBT;


abstract class Code
{
    /**
     * @var string 处理成功
     */
    static $success = 'success';

    /**
     * @var string 保留，待唤醒
     */
    static $buried = 'buried';

    /**
     * @var string 延迟，待特定时间后迁移为ready状态
     */
    static $delayed = 'delayed';
}