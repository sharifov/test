<?php

return [
    'components' => [
        'apiService' => [
            'class' => \modules\rentCar\components\ApiRentCarService::class,
            'url' => '{{ modules.rentCar.config.components.apiService.url:str }}',
            'refid' => '{{ modules.rentCar.config.components.apiService.refid:str }}',
            'api_key' => '{{ modules.rentCar.config.components.apiService.api_key:str }}',
        ],
    ],
];
