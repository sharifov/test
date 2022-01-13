<?php

return [
    'components' => [
        'apiService' => [
            'class' => \modules\hotel\components\ApiHotelService::class,
            'url' => env('MODULES_HOTEL_CONFIG_CONFIG_COMPONENTS_APISERVICE_URL'),
            'username' => env('MODULES_HOTEL_CONFIG_CONFIG_COMPONENTS_APISERVICE_USERNAME'),
            'password' => env('MODULES_HOTEL_CONFIG_CONFIG_COMPONENTS_APISERVICE_PASSWORD'),
        ],
    ],
];
