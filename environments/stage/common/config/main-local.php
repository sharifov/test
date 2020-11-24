<?php

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host={{ common.config.main.components.db.dsn.host:str }};dbname={{ common.config.main.components.db.dsn.dbname:str }}',
            'username' => '{{ common.config.main.components.db.username:str }}',
            'password' => '{{ common.config.main.components.db.password:str }}',
            'charset' => 'utf8mb4',
            'enableSchemaCache' => '{{ common.config.main.components.db.enableSchemaCache:bool }}',
            'schemaCacheDuration' => '{{ common.config.main.components.db.schemaCacheDuration:int }}'
        ],
        'cache' => [
            'class' => 'yii\caching\MemCache',
            'servers' => [
                [
                    'host' => '{{ common.config.main.components.cache.server1.host:str }}',
                    'port' => '{{ common.config.main.components.cache.server1.port:int }}',
                ],
            ],
            'useMemcached' => '{{ common.config.main.components.cache.useMemcached:bool }}',
            'keyPrefix' => 'MemCache'
        ],
        'cacheFile' => [
            'class' => 'yii\caching\FileCache',
            'defaultDuration' => '{{ common.config.main.components.cacheFile.defaultDuration:int }}',
            'gcProbability' => '{{ common.config.main.components.cacheFile.gcProbability:int }}',
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
                'port' => '{{ common.config.main.components.mailer2.port:int }}',
                'encryption' => '{{ common.config.main.components.mailer2.encryption:str }}',
            ],
        ],
        'communication' => [
            'class' => \common\components\CommunicationService::class,
            'url' => '{{ common.config.main.components.communication.url:str }}',
            'username' => '{{ common.config.main.components.communication.username:str }}',
            'password' => '{{ common.config.main.components.communication.password:str }}',
        ],
        'airsearch' => [
            'class' => \common\components\AirSearchService::class,
            'url' => '{{ common.config.main.components.airsearch.url:str }}',
            'username' => '{{ common.config.main.components.airsearch.username:str }}',
            'password' => '{{ common.config.main.components.airsearch.password:str }}',
        ],
        'gaRequestService' => [
            'class' => \common\components\ga\GaRequestService::class,
            'url' => '{{ common.config.main.components.gaRequestService.url:str }}',
        ],
        'centrifugo' => [
            'class'  => \sorokinmedia\centrifugo\Client::class,
            'host'   => '{{ common.config.main.components.centrifugo.host:str }}',
            'secret' => '{{ common.config.main.components.centrifugo.secret:str }}',
            'apikey' => '{{ common.config.main.components.centrifugo.apikey:str }}'
        ],
    ],
];
