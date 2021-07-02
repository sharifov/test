<?php

$config = [
    'name' => 'CRM - DEV',
    'components' => [
        'request' => [
            'cookieValidationKey' => env('frontend.config.main.components.request.cookieValidationKey'),
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
