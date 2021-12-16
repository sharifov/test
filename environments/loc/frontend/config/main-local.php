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
        'allowedIPs' => ['*']
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
        'generators' => [
            'model' => ['class' => \common\components\gii\model\Generator::class],
            'crud' => ['class' => \common\components\gii\crud\Generator::class],
        ],
    ];
}

return $config;
