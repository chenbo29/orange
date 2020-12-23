<?php

return [
    'tubes' => [
        'test' => [
            'worker_num' => 2,
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
    ]
];