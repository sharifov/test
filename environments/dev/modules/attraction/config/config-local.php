<?php

use modules\attraction\components\ApiAttractionService;

return [
    'components' => [
        'apiService' => [
            'class' => ApiAttractionService::class,
            'url' => env('MODULES_ATTRACTION_CONFIG_CONFIG_COMPONENTS_APISERVICE_URL'),
            'apiKey' => env('MODULES_ATTRACTION_CONFIG_CONFIG_COMPONENTS_APISERVICE_APIKEY'),
            'secret' => env('MODULES_ATTRACTION_CONFIG_CONFIG_COMPONENTS_APISERVICE_SECRET'),
        ],
    ],
];
