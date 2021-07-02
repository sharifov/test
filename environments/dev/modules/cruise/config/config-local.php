<?php

return [
    'components' => [
        'apiService' => [
            'class' => \modules\cruise\components\ApiCruiseService::class,
            'url' => env('modules.cruise.config.config.components.apiService.url'),
            'username' => env('modules.cruise.config.config.components.apiService.username'),
            'password' => env('modules.cruise.config.config.components.apiService.password'),
        ],
    ],
];
