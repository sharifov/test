<?php

return [
    'components' => [
        'apiService' => [
            'class' => \modules\attraction\components\ApiAttractionService::class,
            'url' => '{{ modules.attraction.config.components.apiService.url:str }}',
            'apiKey' => '{{ modules.attraction.config.components.apiService.apiKey:str }}',
            'secret' => '{{ modules.attraction.config.components.apiService.secret:str }}',
        ],
    ],
];
