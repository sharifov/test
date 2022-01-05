<?php

return [
    'limitUserConnections' => env('frontend.config.params.limitUserConnections', 'int'),
    'minifiedAssetsEnabled' => true,
    'webSocketServer' => [
        'connectionUrl' => env('frontend.config.params.webSocketServer.connectionUrl'),
    ],
];
