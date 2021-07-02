<?php

return [
    'serviceName' => 'sales-console',

    'webSocketServer' => [
        'host' => env('console.config.params.webSocketServer.host'),
        'port' => env('console.config.params.webSocketServer.port'),
        'mode' => SWOOLE_PROCESS,
        'sockType' => SWOOLE_SOCK_TCP,
        'settings' => [
            'pid_file' => __DIR__ . '/../runtime/swoole.pid',
            'worker_num' => 1,
            'websocket_compression' => true,
            'group' => 'www-data'
        ]
    ]
];
