<?php
return [
    'components' => [
        'apiService' => [
            'class' => \modules\flight\components\api\ApiFlightService::class,
            'url' => '{{ modules.flight.config.components.apiService.url:str }}',
            'username' => '{{ modules.flight.config.components.apiService.username:str }}',
            'password' => '{{ modules.flight.config.components.apiService.password:str }}',
        ],
    ],
    'params' => [

    ],
];