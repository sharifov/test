<?php
return [
    'components' => [
        'apiService' => [
            'class' => \modules\hotel\components\ApiHotelService::class,
            'url' => 'https://dev-hotels.travel-dev.com/api/v1/',
            'username' => 'hotels',
            'password' => '',
        ],
    ],
    'params' => [

    ],
];