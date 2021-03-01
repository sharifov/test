<?php

return [
    'components' => [
        'apiService' => [
            'class' => \modules\attraction\components\ApiAttractionService::class,
            'url' => 'https://api.sandbox.holibob.tech/graphql',
            'apiKey' => '',
            'secret' => '',
        ],
    ],
];
