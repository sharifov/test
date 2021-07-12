<?php

return [
    'components' => [
        'apiService' => [
            'class' => \modules\hotel\components\ApiHotelService::class,
            'url' => env('modules.hotel.config.config.components.apiService.url'),
            'username' => env('modules.hotel.config.config.components.apiService.username'),
            'password' => env('modules.hotel.config.config.components.apiService.password'),
        ],
    ],
];
