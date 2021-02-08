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
            'schemaCacheDuration' => '{{ common.config.main.components.db.schemaCacheDuration:int }}',
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
            'port' => '{{ common.config.main.components.redis.port:int }}',
            'database' => '{{ common.config.main.components.redis.database:int }}',
            'password' => '{{ common.config.main.components.redis.password:str }}',
            'unixSocket' => null,
        ],
        'session' => [
            'class' => \yii\redis\Session::class,
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => '{{ common.config.main.components.cache.redis.hostname:str }}',
                'port' => '{{ common.config.main.components.cache.redis.port:int }}',
                'password' => '{{ common.config.main.components.cache.redis.password:str }}',
                'database' => '{{ common.config.main.components.cache.redis.database:int }}',
                'unixSocket' => null,
            ],
        ],
        'cacheFile' => [
            'class' => 'yii\caching\FileCache',
            'defaultDuration' => 10 * 60,
            'gcProbability' => 100,
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
            'useFileTransport' => false,
        ],
        'communication' => [
            'class' => \common\components\CommunicationService::class,
            'url' => '{{ common.config.main.components.communication.url:str }}',
            'username' => '{{ common.config.main.components.communication.username:str }}',
            'password' => '{{ common.config.main.components.communication.password:str }}',
            'voipApiUsername' => '{{ common.config.main.components.communication.voipApiUsername:str }}',
            'xAccelRedirectUrl' => '{{ common.config.main.components.communication.xAccelRedirectUrl:str }}',
            'recordingUrl' => '{{ common.config.main.components.communication.recordingUrl:str }}'
        ],
        'airsearch' => [
            'class' => \common\components\AirSearchService::class,
            'url' => '{{ common.config.main.components.airsearch.url:str }}',
            'username' => '{{ common.config.main.components.airsearch.username:str }}',
            'password' => '{{ common.config.main.components.airsearch.password:str }}',
            'searchQuoteEndpoint' => 'v1/internalsearch',
        ],
        'currency' => [
            'class' => \common\components\CurrencyService::class,
            'url' => '{{ common.config.main.components.currency.url:str }}',
            'username' => '{{ common.config.main.components.currency.username:str }}',
            'password' => '{{ common.config.main.components.currency.password:str }}',
        ],
        'rchat' => [
            'class' => \common\components\RocketChat::class,
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
            'host' => '{{ common.config.params.queue.host:str }}',
            'port' => '{{ common.config.params.queue.port:int }}',
            'tube' => 'queue_sms_job',
        ],
        'queue_email_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => '{{ common.config.params.queue.host:str }}',
            'port' => '{{ common.config.params.queue.port:int }}',
            'tube' => 'queue_email_job',
        ],
        'queue_phone_check' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => '{{ common.config.params.queue.host:str }}',
            'port' => '{{ common.config.params.queue.port:int }}',
            'tube' => 'queue_phone_check',
        ],
        'queue_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => '{{ common.config.params.queue.host:str }}',
            'port' => '{{ common.config.params.queue.port:int }}',
            'tube' => 'queue_job',
        ],
        'queue_system_services' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => '{{ common.config.params.queue.host:str }}',
            'port' => '{{ common.config.params.queue.port:int }}',
            'tube' => 'queue_system_services',
        ],
        'queue_client_chat_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => '{{ common.config.params.queue.host:str }}',
            'port' => '{{ common.config.params.queue.port:int }}',
            'tube' => 'queue_client_chat_job',
            'as idAccess' => sales\behaviors\JobIdAccessBehavior::class
        ],
        'queue_virtual_cron' => [
            'class' => \kivork\VirtualCron\Queue\Queue::class,
            'host' => '{{ common.config.params.queue.host:str }}',
            'port' => '{{ common.config.params.queue.port:int }}',
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
                'password' => '{{ common.config.main.components.prometheus.redisOptions.password:str }}',
                'database' => '{{ common.config.main.components.prometheus.redisOptions.database:int }}',
                'timeout' => 0.1,
                'read_timeout' => 10,
                'persistent_connections' => false,
            ],
            'useHttpBasicAuth' => '{{ common.config.main.components.prometheus.useHttpBasicAuth:bool }}',
            'authUsername' => '{{ common.config.main.components.prometheus.authUsername:str }}',
            'authPassword' => '{{ common.config.main.components.prometheus.authPassword:str }}',
        ],
    ],
];
