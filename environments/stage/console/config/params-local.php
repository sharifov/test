<?php

return [
    'webSocketServer' => [
        'host' => env('CONSOLE_CONFIG_PARAMS_WEBSOCKETSERVER_HOST'),
        'port' => env('CONSOLE_CONFIG_PARAMS_WEBSOCKETSERVER_PORT'),
        'mode' => 3, //SWOOLE_PROCESS,
        'sockType' => 1, //SWOOLE_SOCK_TCP,
        'settings' => [
            'pid_file' => __DIR__ . '/../runtime/swoole.pid',
            'worker_num' => 1,
            'websocket_compression' => true,
            'group' => 'www-data',
            'open_tcp_keepalive' => true,  // Enables TCP keep alive checks
            'tcp_keepidle' => 60,  // In seconds, the time a connection needs to remain idle before TCP starts sending keep alive probes
            'tcp_keepinterval' => 3,  // In seconds, the time between individual keep alive probes/checks
            'tcp_keepcount' => 3,  // The maximum number of keep alive probes/checks to send before dropping the connection, classing it as dead or broken
            'heartbeat_idle_time' => 60,  // In seconds, indicates that the connection has not sent any data to the server within this time, so the connection will be closed
            'heartbeat_check_interval' => 15,  // In seconds, the interval between how long to wait before checking idle connections again
        ]
    ]
];
