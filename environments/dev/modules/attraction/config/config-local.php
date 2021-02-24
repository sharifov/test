<?php

return [
    'components' => [
        'apiService' => [
            'class' => \modules\flight\components\api\ApiFlightService::class,
            'url' => '{{ modules.attraction.config.components.apiService.url:str }}',
            'username' => '{{ modules.attraction.config.components.apiService.username:str }}',
            'password' => '{{ modules.attraction.config.components.apiService.password:str }}',
        ],
    ],
];
