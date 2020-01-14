<?php
return [
    'components' => [
        'apiService' => [
            'class' => \modules\flight\components\api\ApiFlightService::class,
            'url' => 'https://dev-hotels.travel-dev.com/api/v1/',
            'username' => 'hotels',
            'password' => '',
        ],
    ],
    'params' => [

    ],
];