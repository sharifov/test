<?php

use modules\attraction\components\ApiAttractionService;

return [
    'components' => [
        'apiService' => [
            'class' => ApiAttractionService::class,
            'url' => env('modules.attraction.config.config.components.apiService.url'),
            'apiKey' => env('modules.attraction.config.config.components.apiService.apiKey'),
            'secret' => env('modules.attraction.config.config.components.apiService.secret'),
        ],
    ],
];
