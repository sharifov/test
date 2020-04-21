<?php
return [
    'adminEmail' => 'admin@example.com',
    'serviceName' => 'sales-console',
    'webSocketServer' => [
        'host' => 'localhost',
        'port' => 8080,
        'mode' => SWOOLE_PROCESS,
        'sockType' => SWOOLE_SOCK_TCP,
        'settings' => [
            // https://www.swoole.co.uk/docs/modules/swoole-server/configuration
            'pid_file' => __DIR__ . '/../runtime/swoole.pid',
            'worker_num' => 1,
            //'daemonize' => 0,
            //'task_worker_num' => 2,
            'group' => 'www-data'
        ]
    ]
];
