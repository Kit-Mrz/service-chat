<?php

return [
    'default' => [
        'driver'         => Hyperf\AsyncQueue\Driver\RedisDriver::class,
        'redis'          => [
            'pool' => 'default', //redis 连接池
        ],
        'channel'        => 'queue', // 队列前缀
        'timeout'        => 2, // pop 消息的超时时间
        'retry_seconds'  => 5, // 失败后重新尝试间隔
        'handle_timeout' => 10, // 消息处理超时时间
        'processes'      => 1, // 消费进程数
        'concurrent'     => [
            'limit' => 5, // 同时处理消息数
        ],
    ],
];
