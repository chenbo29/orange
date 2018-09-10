<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-5-28
 * Time: 下午5:44
 */

return [
    'tubes' => [
        'test' => [
            'worker_num' => 3,
            'class' => \SWBT\Worker\TestWorker::class
        ]
    ],
    'beanstalkd' => [
        'host' => '127.0.0.1',
        'port' => '11300',
        'reserve_timeout' => 5
    ],
    'pid' => [
        'file_path' => 'storage/master.pid'
    ],
    'log' => [
        'name' => 'SWBT',
        'path' => 'storage/logs/'
    ]
];