<?php

return [
    'id' => 'app-common-tests',
    'basePath' => dirname(__DIR__),
    'components' => [
        'db' => [
            'dsn' => 'sqlite:' . __DIR__ . '/../data/db.sqlite',
            'charset' => 'utf8mb4',
            'enableSchemaCache' => false
        ],
//        'user' => [
//            'class' => 'yii\web\User',
//            'identityClass' => 'common\models\User',
//        ],
//        'request' => [
//            'cookieValidationKey' => 'test',
//        ],
    ],
];
