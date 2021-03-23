<?php

return [
    'components' => [
        'apiService' => [
            'class' => \modules\cruise\components\ApiCruiseService::class,
            'url' => '{{ modules.cruise.config.components.apiService.url:str }}',
            'username' => '{{ modules.cruise.config.components.apiService.username:str }}',
            'password' => '{{ modules.cruise.config.components.apiService.password:str }}',
        ],
    ],
];
