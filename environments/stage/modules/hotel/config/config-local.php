<?php
return [
    'components' => [
        'apiService' => [
            'class' => \modules\hotel\components\ApiHotelService::class,
            'url' => '{{ modules.hotel.config.components.apiService.url:str }}',
            'username' => '{{ modules.hotel.config.components.apiService.username:str }}',
            'password' => '{{ modules.hotel.config.components.apiService.password:str }}',
        ],
    ],
    'params' => [

    ],
];