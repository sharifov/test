<?php

return [
    'limitUserConnections' => env('frontend.config.params.limitUserConnections', 'int'),
    'minifiedAssetsEnabled' => false,
    'webSocketServer' => [
        'connectionUrl' => env('frontend.config.params.webSocketServer.connectionUrl'),
    ],
];
