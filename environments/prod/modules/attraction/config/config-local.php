<?php

return [
    'components' => [
        'apiService' => [
            'class' => \modules\attraction\components\ApiAttractionService::class,
            'url' => env('modules.attraction.config.config.components.apiService.url'),
            'apiKey' => env('modules.attraction.config.config.components.apiService.apiKey'),
            'secret' => env('modules.attraction.config.config.components.apiService.secret'),
        ],
    ],
];
