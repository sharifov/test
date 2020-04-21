<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host={{ common.config.main.components.db.dsn.host:str }};dbname={{ common.config.main.components.db.dsn.dbname:str }}',
            'username' => '{{ common.config.main.components.db.username:str }}',
            'password' => '{{ common.config.main.components.db.password:str }}',
            'charset' => 'utf8mb4',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 3600
        ],
        'cache' => [
            'class' => 'yii\caching\MemCache',
            'servers' => [
                [
                    'host' => '127.0.0.1',
                    'port' => 11211,
                ],
            ],
            'useMemcached' => true,
            'keyPrefix' => 'MemCache'
        ],
        'cacheFile' => [
            'class' => 'yii\caching\FileCache',
            'defaultDuration' => 10 * 60,
            'gcProbability' => 100,
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
        'mailer2' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => '{{ common.config.main.components.mailer2.host:str }}',
                'username' => '{{ common.config.main.components.mailer2.username:str }}',
                'password' => '{{ common.config.main.components.mailer2.password:str }}',
                'port' => 587,
                'encryption' => 'tls',
            ],
        ],
        'communication' => [
            'class' => \common\components\CommunicationService::class,
            'url' => '{{ common.config.main.components.communication.url:str }}',
            'username' => '{{ common.config.main.components.communication.username:str }}',
            'password' => '{{ common.config.main.components.communication.password:str }}',
        ],
    ],
];
