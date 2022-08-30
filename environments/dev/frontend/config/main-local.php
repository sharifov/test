<?php

$config = [
    'name' => 'CRM - DEV',
    'components' => [
        'request' => [
            'cookieValidationKey' => env('FRONTEND_CONFIG_MAIN_COMPONENTS_REQUEST_COOKIEVALIDATIONKEY'),
        ],
    ],
];

$config['bootstrap'][] = 'debug';
$config['modules']['debug'] = [
    'class' => 'yii\debug\Module',
    'allowedIPs' => ['127.0.0.1', '*']  //allowing ip's
];

return $config;
