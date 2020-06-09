<?php
return [
    'serviceName' => '{{ console.config.params.serviceName:str }}',
    'webSocketServer' => [
        'host' => '{{ console.config.params.webSocketServer.host:str }}',
        'port' => '{{ console.config.params.webSocketServer.port:int }}',
        'mode' => SWOOLE_PROCESS,
        'sockType' => SWOOLE_SOCK_TCP,
        'settings' => [
            // https://www.swoole.co.uk/docs/modules/swoole-server/configuration
            'pid_file' => __DIR__ . '/../runtime/swoole.pid',
            'worker_num' => 1,
            'websocket_compression' => true,
            //'daemonize' => 0,
            //'task_worker_num' => 2,
            'group' => 'www-data'
        ]
    ]
];
