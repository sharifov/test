<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=mysql;dbname=sale',
            'username' => 'sale',
            'password' => 'SalePasswd1!',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'communication' => [
            'class' => \common\components\CommunicationService::class,
            'url' => 'https://communication-dev.api.travelinsides.com/v1/',
            'username' => 'sales',
            'password' => 'Sales2018!',
        ],
    ],
];
