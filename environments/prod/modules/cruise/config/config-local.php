<?php

return [
    'components' => [
        'apiService' => [
            'class' => \modules\cruise\components\ApiCruiseService::class,
            'url' => env('MODULES_CRUISE_CONFIG_CONFIG_COMPONENTS_APISERVICE_URL'),
            'username' => env('MODULES_CRUISE_CONFIG_CONFIG_COMPONENTS_APISERVICE_USERNAME'),
            'password' => env('MODULES_CRUISE_CONFIG_CONFIG_COMPONENTS_APISERVICE_PASSWORD'),
        ],
    ],
];
