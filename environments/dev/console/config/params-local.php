<?php
return [
    'serviceName' => '{{ console.config.params.serviceName:str }}',
    'sync' => [
        'ver' => '{{ console.config.params.sync.ver:str }}',
        'apiKey' => '{{ console.config.params.sync.apiKey:str }}',
        'serverUrl' => '{{ console.config.params.sync.serverUrl:str }}'
    ],
    'AWS_MAILER' => [
        'host' => '{{ console.config.params.AWS_MAILER.host:str }}',
        'port' => '{{ console.config.params.AWS_MAILER.port:int }}',
        'security' => '{{ console.config.params.AWS_MAILER.security:str }}',
        'username' => '{{ console.config.params.AWS_MAILER.username:str }}',
        'password' => '{{ console.config.params.AWS_MAILER.password:str }}'
    ],
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
