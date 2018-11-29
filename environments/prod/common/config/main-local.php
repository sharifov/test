<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=sale',
            'username' => 'sale',
            'password' => ')*YB6N)(c0ejip3',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
        'communication' => [
            'class' => \common\components\CommunicationService::class,
            'url' => 'https://communication.api.travelinsides.com/v1/',
            'username' => 'sales',
            'password' => 'Sales2018!',
        ],
    ],
];
