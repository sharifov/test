<?php
return [
    'components' => [
        'apiService' => [
            'class' => \modules\flight\components\ApiFlightService::class,
            'url' => 'https://dev-flight.travel-dev.com/api/v1/',
            'username' => 'flight',
            'password' => '',
        ],
    ],
    'params' => [

    ],
];