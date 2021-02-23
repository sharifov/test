<?php

return [
    'components' => [
        'apiService' => [
            'class' => \modules\rentCar\components\ApiRentCarService::class,
            'url' => '{{ modules.rentCar.config.components.apiService.url:str }}',
            'username' => '{{ modules.rentCar.config.components.apiService.username:str }}',
            'password' => '{{ modules.rentCar.config.components.apiService.password:str }}',
        ],
    ],
];
