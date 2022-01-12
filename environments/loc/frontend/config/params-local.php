<?php

return [
    'limitUserConnections' => env('FRONTEND_CONFIG_PARAMS_LIMITUSERCONNECTIONS', 'int'),
    'minifiedAssetsEnabled' => false,
    'webSocketServer' => [
        'connectionUrl' => env('FRONTEND_CONFIG_PARAMS_WEBSOCKETSERVER_CONNECTIONURL'),
    ],
];
