<?php

return [
    'limitUserConnections' => env('FRONTEND_CONFIG_PARAMS_LIMITUSERCONNECTIONS', 'int'),
    'minifiedAssetsEnabled' => true,
    'webSocketServer' => [
        'connectionUrl' => env('FRONTEND_CONFIG_PARAMS_WEBSOCKETSERVER_CONNECTIONURL'),
    ],
];
