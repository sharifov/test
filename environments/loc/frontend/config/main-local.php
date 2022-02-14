<?php

$config = [
    'name' => 'CRM - DEV',
    'components' => [
        'request' => [
            'cookieValidationKey' => env('FRONTEND_CONFIG_MAIN_COMPONENTS_REQUEST_COOKIEVALIDATIONKEY'),
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
//        'generators' => [
//            'model' => ['class' => \common\components\gii\model\Generator::class],
//            'crud' => ['class' => \common\components\gii\crud\Generator::class],
//        ],
    ];
}

return $config;
