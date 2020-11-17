<?php
return [
    'name' => 'CRM - DEV',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host={{ common.config.main.components.db.dsn.host:str }};dbname={{ common.config.main.components.db.dsn.dbname:str }}',
            'username' => '{{ common.config.main.components.db.username:str }}',
            'password' => '{{ common.config.main.components.db.password:str }}',
            'charset' => 'utf8mb4',
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
        'webApiCache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@webapi/runtime/cache'
        ],
        'consoleCache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@console/runtime/cache'
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => true,
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
            'url2' => '{{ common.config.main.components.communication.url2:str }}',
            'username' => '{{ common.config.main.components.communication.username:str }}',
            'password' => '{{ common.config.main.components.communication.password:str }}',
            'recording_url' => '{{ common.config.main.components.communication.recording_url:str }}',
            'voipApiUsername' => 'sales'
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

        'travelServices' => [
            'class' => \common\components\TravelServices::class,
            'url' => '{{ common.config.main.components.travelServices.url:str }}',
            'username' => '{{ common.config.main.components.travelServices.username:str }}',
            'password' => '{{ common.config.main.components.travelServices.password:str }}',
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
        'queue_system_services' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => '{{ common.config.main.components.queue_system_services.host:str }}',
            'port' => '{{ common.config.main.components.queue_system_services.port:int }}',
            'tube' => 'queue_system_services',
        ],
        'queue_client_chat_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => '{{ common.config.main.components.queue_client_chat_job.host:str }}',
            'port' => '{{ common.config.main.components.queue_client_chat_job.port:int }}',
            'tube' => 'queue_client_chat_job',
            'as idAccess' => sales\behaviors\JobIdAccessBehavior::class
        ],
        'queue_virtual_cron' => [
            'class' => \kivork\VirtualCron\Queue\Queue::class,
            'host' => '{{ common.config.main.components.queue_virtual_cron.host:str }}',
            'port' => '{{ common.config.main.components.queue_virtual_cron.port:int }}',
            'tube' => 'queue_virtual_cron',
        ],
        'telegram' => [
            'class' => \aki\telegram\Telegram::class,
            'botUsername' => '{{ common.config.main.components.telegram.botUsername:str }}',
            'botToken' => '{{ common.config.main.components.telegram.botToken:str }}',
        ],
        'gaRequestService' => [
            'class' => \common\components\ga\GaRequestService::class,
            'url' => '{{ common.config.main.components.gaRequestService.url:str }}',
        ],
    ],
];
