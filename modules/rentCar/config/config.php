<?php

return [
    'components' => [
        'apiService' => [
            'class' => \modules\rentCar\components\ApiRentCarService::class,
            'url' => 'https://api-sandbox.rezserver.com/api/car/', /* TODO:: for presentation */
            'refid' => '8965',
            'api_key' => '69348609cb746791fe82aab86634c3e6',
        ],
    ],
];
