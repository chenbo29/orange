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
    'log' => [
        'name' => 'SWBT',
        'path' => 'storage/logs/'
    ]
];