<?php

return [
    'components' => [
        'apiService' => [
            'class' => \modules\rentCar\components\ApiRentCarService::class,
            'url' => env('MODULES_RENTCAR_CONFIG_CONFIG_COMPONENTS_APISERVICE_URL'),
            'refid' => env('MODULES_RENTCAR_CONFIG_CONFIG_COMPONENTS_APISERVICE_REFID'),
            'api_key' => env('MODULES_RENTCAR_CONFIG_CONFIG_COMPONENTS_APISERVICE_APIKEY'),
        ],
    ],
];
