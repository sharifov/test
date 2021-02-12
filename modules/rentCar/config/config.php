<?php

return [
    'components' => [
        'apiService' => [
            'class' => \modules\rentCar\components\ApiRentCarService::class,
            'url' => 'https://stage-communication-api.travel-dev.com/v1/',
            'username' => 'sales',
            'password' => 'Sales2018!',
        ],
    ],
    'params' => [

    ],
];
