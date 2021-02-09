<?php

return [
    'serviceName' => 'sales-console',

    'webSocketServer' => [
        'host' => '{{ console.config.params.webSocketServer.host:str }}',
        'port' => '{{ console.config.params.webSocketServer.port:int }}',
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
