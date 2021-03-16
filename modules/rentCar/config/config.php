<?php

return [
    'components' => [
        'apiService' => [
            'class' => \modules\rentCar\components\ApiRentCarService::class,
            'url' => 'https://api-sandbox.rezserver.com/api/car/',
            'refid' => '',
            'api_key' => '',
        ],
    ],
];
