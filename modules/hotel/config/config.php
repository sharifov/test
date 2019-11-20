<?php
return [
    'components' => [
        'service' => [
            'class' => \modules\hotel\components\ApiHotelService::class,
            'url' => 'https://dev-hotels.travel-dev.com/api/v1/',
            'username' => 'crm',
            'password' => '123456789',
        ],
    ],
    'params' => [

    ],
];