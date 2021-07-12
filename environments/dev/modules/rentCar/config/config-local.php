<?php

return [
    'components' => [
        'apiService' => [
            'class' => \modules\rentCar\components\ApiRentCarService::class,
            'url' => env('modules.rentCar.config.config.components.apiService.url'),
            'refid' => env('modules.rentCar.config.config.components.apiService.refid'),
            'api_key' => env('modules.rentCar.config.config.components.apiService.api_key'),
        ],
    ],
];
