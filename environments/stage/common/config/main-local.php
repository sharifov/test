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
        'db_postgres' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host={{ common.config.main.components.db_postgres.dsn.host:str }};port={{ common.config.main.components.db_postgres.dsn.port:int }};dbname={{ common.config.main.components.db_postgres.dsn.dbname:str }}',
            'username' => '{{ common.config.main.components.db_postgres.username:str }}',
            'password' => '{{ common.config.main.components.db_postgres.password:str }}',
            'charset' => 'utf8',
            'enableSchemaCache' => '{{ common.config.main.components.db_postgres.enableSchemaCache:bool }}',
            'schemaCacheDuration' => '{{ common.config.main.components.db_postgres.schemaCacheDuration:int }}',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
        'communication' => [
            'class' => \common\components\CommunicationService::class,
            'url' => '{{ common.config.main.components.communication.url:str }}',
            'username' => '{{ common.config.main.components.communication.username:str }}',
            'password' => '{{ common.config.main.components.communication.password:str }}',
            'recording_url' => '{{ common.config.main.components.communication.recording_url:str }}',
            'voipApiUsername' => '{{ common.config.main.components.communication.voipApiUsername:str }}',
        ],
        'airsearch' => [
            'class' => \common\components\AirSearchService::class,
            'url' => '{{ common.config.main.components.airsearch.url:str }}',
            'username' => '{{ common.config.main.components.airsearch.username:str }}',
            'password' => '{{ common.config.main.components.airsearch.password:str }}',
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
        'telegram' => [
            'class' => \aki\telegram\Telegram::class,
            'botUsername' => '{{ common.config.main.components.telegram.botUsername:str }}',
            'botToken' => '{{ common.config.main.components.telegram.botToken:str }}',
        ],
        'currency' => [
            'class' => \common\components\CurrencyService::class,
            'url' => '{{ common.config.main.components.currency.url:str }}',
            'username' => '{{ common.config.main.components.currency.username:str }}',
            'password' => '{{ common.config.main.components.currency.password:str }}',
        ],
        'rchat' => [
            'class' => \common\components\RocketChat::class,
            'url' => '{{ common.config.main.components.rchat.url:str }}',
            'username' => '{{ common.config.main.components.rchat.username:str }}',
            'password' => '{{ common.config.main.components.rchat.password:str }}',
            'host' => '{{ common.config.main.components.rchat.host:str }}',
        ],
        'chatBot' => [
            'class' => \common\components\ChatBot::class,
            'url' => '{{ common.config.main.components.chatBot.url:str }}',
            'username' => '{{ common.config.main.components.chatBot.username:str }}',
            'password' => '{{ common.config.main.components.chatBot.password:str }}',
        ],
        'gaRequestService' => [
            'class' => \common\components\ga\GaRequestService::class,
            'url' => '{{ common.config.main.components.gaRequestService.url:str }}',
        ],
        'travelServices' => [
            'class' => \common\components\TravelServices::class,
            'url' => '{{ common.config.main.components.travelServices.url:str }}',
            'username' => '{{ common.config.main.components.travelServices.username:str }}',
            'password' => '{{ common.config.main.components.travelServices.password:str }}',
        ],
        'centrifugo' => [
            'class'  => \sorokinmedia\centrifugo\Client::class,
            'host'   => '{{ common.config.main.components.centrifugo.host:str }}',
            'secret' => '{{ common.config.main.components.centrifugo.secret:str }}',
            'apikey' => '{{ common.config.main.components.centrifugo.apikey:str }}'
        ],
    ],
];
