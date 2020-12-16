<?php

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=crm_sale_kivork',
            'username' => 'root',
            'password' => 'root',
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
            'url' => 'http://api.communication.office.test/v1/',
            'username' => 'sales',
            'password' => 'Sales2018!',
        ],
        'gaRequestService' => [
            'class' => \common\components\ga\GaRequestService::class,
            'url' => 'https://www.google-analytics.com/collect',
        ],
        'centrifugo' => [
            'class'  => \sorokinmedia\centrifugo\Client::class,
            'host'   => '{{ common.config.main.components.centrifugo.host:str }}',
            'secret' => '{{ common.config.main.components.centrifugo.secret:str }}',
            'apikey' => '{{ common.config.main.components.centrifugo.apikey:str }}'
        ],
        'prometheus' => [
            'class' => \kivork\PrometheusClient\components\PrometheusClient::class,
            'redisOptions' => [
                'prefix' => php_uname('n'),
                'host' => '{{ common.config.main.components.prometheus.redisOptions.host:str }}',
                'port' => '{{ common.config.main.components.prometheus.redisOptions.port:int }}',
                'password' => null,
                'timeout' => 0.1,
                'read_timeout' => 10,
                'persistent_connections' => false,
                'database' => 3,
            ],
            'useHttpBasicAuth' => '{{ common.config.main.components.prometheus.useHttpBasicAuth:bool }}',
            'authUsername' => '{{ common.config.main.components.prometheus.authUsername:str }}',
            'authPassword' => '{{ common.config.main.components.prometheus.authPassword:str }}',
        ],
    ],
];
