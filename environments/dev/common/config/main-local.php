<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=mysql;dbname=sale',
            'username' => 'sale',
            'password' => 'SalePasswd1!',
            'charset' => 'utf8mb4',
        ],
        'db_postgres' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=127.0.0.1;port=5432;dbname=crm',
            'username' => 'postgres',
            'password' => 'postgres',
            'charset' => 'utf8',
            'enableSchemaCache' => false,
            'schemaCacheDuration' => 3600,
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
        'airsearch' => [
            'class' => \common\components\AirSearchService::class,
            'url' => 'https://searchapi-dev.travel-dev.com/',
            'username' => 'SAL101',
            'password' => 'c940e3484fe9fcc73ed12a7fcec469b4',
        ],
    ],
];
