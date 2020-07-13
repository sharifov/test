<?php

use yii\db\Connection;

return [
    'components' => [
        'db' => [
            'class' => Connection::class,
            'dsn' => 'mysql:host={{ common.config.main.components.db.dsn.host:str }};dbname={{ common.config.main.components.db.dsn.dbname:str }}',
            'username' => '{{ common.config.main.components.db.username:str }}',
            'password' => '{{ common.config.main.components.db.password:str }}',
            'charset' => 'utf8mb4',
            'enableSchemaCache' => '{{ common.config.main.components.db.enableSchemaCache:bool }}',
            'schemaCacheDuration' => '{{ common.config.main.components.db.schemaCacheDuration:int }}',
        ],
        'db2' => [
            'class' => Connection::class,
            'dsn' => 'mysql:host={{ common.config.main.components.db2.dsn.host:str }};dbname={{ common.config.main.components.db2.dsn.dbname:str }}',
            'username' => '{{ common.config.main.components.db2.username:str }}',
            'password' => '{{ common.config.main.components.db2.password:str }}',
            'charset' => 'utf8mb4',
            'enableSchemaCache' => '{{ common.config.main.components.db2.enableSchemaCache:bool }}',
            'schemaCacheDuration' => '{{ common.config.main.components.db2.schemaCacheDuration:int }}',

            'slaveConfig' => [
                'username' => '{{ common.config.main.components.db2.slaveConfig.username:str }}',
                'password' => '{{ common.config.main.components.db2.slaveConfig.password:str }}',
                'attributes' => [
                    PDO::ATTR_TIMEOUT => 10,
                ],
            ],

            'slaves' => [
                [
                    'dsn' => 'mysql:host={{ common.config.main.components.db2.slave1.dsn.host:str }};dbname={{ common.config.main.components.db2.slave1.dsn.dbname:str }}',
                ],
            ],
        ],
        'db_postgres' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host={{ common.config.main.components.db_postgres.dsn.host:str }};port={{ common.config.main.components.db_postgres.dsn.port:int }};dbname={{ common.config.main.components.db_postgres.dsn.dbname:str }}',
            'username' => '{{ common.config.main.components.db_postgres.username:str }}',
            'password' => '{{ common.config.main.components.db_postgres.password:str }}',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 3600,
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'timeZone' => 'Europe/Chisinau',
            'defaultTimeZone' => 'Europe/Chisinau',
            'dateFormat' => 'php:d-M-Y',
            'datetimeFormat' => 'php:d-M-Y H:i:s',
            'timeFormat' => 'php:H:i',

            'thousandSeparator' => ',',
            'decimalSeparator' => '.',
        ],
        'formatter_search' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'php:d-M-Y',
            'datetimeFormat' => 'php:d-M-Y H:i:s',
            'timeFormat' => 'php:H:i',

            'thousandSeparator' => ',',
            'decimalSeparator' => '.',
        ],
        'redis' => [
            'class' => \yii\redis\Connection::class,
            'hostname' => '{{ common.config.main.components.redis.hostname:str }}',
            'unixSocket' => null,
            'port' => '{{ common.config.main.components.redis.port:int }}',
            'database' => 0,
        ],
        'session' => [
            'class' => \yii\redis\Session::class,
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
            'useFileTransport' => false,
        ],

        'communication' => [
            'class' => \common\components\CommunicationService::class,
            'url' => '{{ common.config.main.components.communication.url:str }}',
            'url2' => '{{ common.config.main.components.communication.url2:str }}',
            'username' => '{{ common.config.main.components.communication.username:str }}',
            'password' => '{{ common.config.main.components.communication.password:str }}',
            'recording_url' => '{{ common.config.main.components.communication.recording_url:str }}',
        ],

        'airsearch' => [
            'class' => \common\components\AirSearchService::class,
            'url' => '{{ common.config.main.components.airsearch.url:str }}',
            'username' => '{{ common.config.main.components.airsearch.username:str }}',
            'password' => '{{ common.config.main.components.airsearch.password:str }}',
        ],

        'currency' => [
            'class' => \common\components\CurrencyService::class,
            'url' => '{{ common.config.main.components.currency.url:str }}',
            'username' => '{{ common.config.main.components.currency.username:str }}',
            'password' => '{{ common.config.main.components.currency.password:str }}',
        ],

        'queue_sms_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => '{{ common.config.main.components.queue_sms_job.host:str }}',
            'port' => '{{ common.config.main.components.queue_sms_job.port:int }}',
            'tube' => 'queue_sms_job',
        ],
        'queue_email_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => '{{ common.config.main.components.queue_email_job.host:str }}',
            'port' => '{{ common.config.main.components.queue_email_job.port:int }}',
            'tube' => 'queue_email_job',
        ],
        'queue_phone_check' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => '{{ common.config.main.components.queue_phone_check.host:str }}',
            'port' => '{{ common.config.main.components.queue_phone_check.port:int }}',
            'tube' => 'queue_phone_check',
        ],
        'queue_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => '{{ common.config.main.components.queue_job.host:str }}',
            'port' => '{{ common.config.main.components.queue_job.port:int }}',
            'tube' => 'queue_job',
        ],
        'telegram' => [
            'class' => \aki\telegram\Telegram::class,
            'botUsername' => '{{ common.config.main.components.telegram.botUsername:str }}',
            'botToken' => '{{ common.config.main.components.telegram.botToken:str }}',
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
    ],
];
