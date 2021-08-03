<?php

return [
    'name' => 'CRM - STAGE',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . env('common.config.main.components.db.dsn.host') . ';dbname=' . env('common.config.main.components.db.dsn.dbname'),
            'username' => env('common.config.main.components.db.username'),
            'password' => env('common.config.main.components.db.password'),
            'charset' => 'utf8mb4',
            'enableSchemaCache' => env('common.config.main.components.db.enableSchemaCache', 'bool'),
            'schemaCacheDuration' => env('common.config.main.components.db.schemaCacheDuration', 'int'),
        ],
        'db_postgres' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=' . env('common.config.main.components.db_postgres.dsn.host') . ';port=' . env('common.config.main.components.db_postgres.dsn.port') . ';dbname=' . env('common.config.main.components.db_postgres.dsn.dbname'),
            'username' => env('common.config.main.components.db_postgres.username'),
            'password' => env('common.config.main.components.db_postgres.password'),
            'charset' => 'utf8',
            'enableSchemaCache' => env('common.config.main.components.db_postgres.enableSchemaCache', 'bool'),
            'schemaCacheDuration' => env('common.config.main.components.db_postgres.schemaCacheDuration', 'int'),
        ],
        'db_slave' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . env('common.config.main.components.db_slave.dsn.host') . ';dbname=' . env('common.config.main.components.db_slave.dsn.dbname'),
            'username' => env('common.config.main.components.db_slave.username'),
            'password' => env('common.config.main.components.db_slave.password'),
            'charset' => 'utf8mb4',
            'enableSchemaCache' => env('common.config.main.components.db_slave.enableSchemaCache', 'bool'),
            'schemaCacheDuration' => env('common.config.main.components.db_slave.schemaCacheDuration', 'int'),

            'slaveConfig' => [
                'username' => env('common.config.main.components.db_slave.slaveConfig.username'),
                'password' => env('common.config.main.components.db_slave.slaveConfig.password'),
            ],

            'slaves' => [
                ['dsn' => 'mysql:host=' . env('common.config.main.components.db_slave.slaves.0.dsn.host') . ';port=3306;dbname=' . env('common.config.main.components.db_slave.slaves.0.dsn.dbname'),]
            ]
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
            'hostname' => env('common.config.main.components.redis.hostname'),
            'port' => env('common.config.main.components.redis.port'),
            'database' => env('common.config.main.components.redis.database', 'int'),
            'password' => env('common.config.main.components.redis.password'),
            'unixSocket' => null,
        ],
        'session' => [
            'class' => \yii\redis\Session::class,
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => env('common.config.main.components.cache.redis.hostname'),
                'port' => env('common.config.main.components.cache.redis.port'),
                'password' => env('common.config.main.components.cache.redis.password'),
                'database' => env('common.config.main.components.cache.redis.database', 'int'),
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
            'host' => env('common.config.main.components.communication.host'),
            'url' => env('common.config.main.components.communication.url'),
            'username' => env('common.config.main.components.communication.username'),
            'password' => env('common.config.main.components.communication.password'),
            'voipApiUsername' => env('common.config.main.components.communication.voipApiUsername'),
            'xAccelRedirectUrl' => env('common.config.main.components.communication.xAccelRedirectUrl'),
            'recordingUrl' => env('common.config.main.components.communication.recordingUrl')
        ],
        'hybrid' => [
            'class' => \common\components\HybridService::class,
            'username' => env('common.config.main.components.hybrid.username'),
            'password' => env('common.config.main.components.hybrid.password'),
            'webHookEndpoint' => env('common.config.main.components.hybrid.webHookEndpoint'),
        ],
        'airsearch' => [
            'class' => \common\components\AirSearchService::class,
            'url' => env('common.config.main.components.airsearch.url'),
            'username' => env('common.config.main.components.airsearch.username'),
            'password' => env('common.config.main.components.airsearch.password'),
            'searchQuoteEndpoint' => 'v1/internalsearch',
            'searchQuoteByKeyEndpoint' => 'v1/result',
        ],
        'currency' => [
            'class' => \common\components\CurrencyService::class,
            'url' => env('common.config.main.components.currency.url'),
            'username' => env('common.config.main.components.currency.username'),
            'password' => env('common.config.main.components.currency.password'),
        ],
        'rchat' => [
            'class' => \common\components\RocketChat::class,
            'username' => env('common.config.main.components.rchat.username'),
            'password' => env('common.config.main.components.rchat.password'),
            'host' => env('common.config.main.components.rchat.host'),
            'url' => env('common.config.main.components.rchat.url'),
            'apiServer' => env('common.config.main.components.rchat.apiServer'),
            'chatApiScriptUrl' => env('common.config.main.components.rchat.chatApiScriptUrl')
        ],
        'chatBot' => [
            'class' => \common\components\ChatBot::class,
            'url' => env('common.config.main.components.chatBot.url'),
            'username' => env('common.config.main.components.chatBot.username'),
            'password' => env('common.config.main.components.chatBot.password'),
        ],
        'travelServices' => [
            'class' => \common\components\TravelServices::class,
            'url' => env('common.config.main.components.travelServices.url'),
            'username' => env('common.config.main.components.travelServices.username'),
            'password' => env('common.config.main.components.travelServices.password'),
        ],
        'queue_sms_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => env('common.config.params.queue.host'),
            'port' => env('common.config.params.queue.port'),
            'tube' => 'queue_sms_job',
        ],
        'queue_email_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => env('common.config.params.queue.host'),
            'port' => env('common.config.params.queue.port'),
            'tube' => 'queue_email_job',
        ],
        'queue_phone_check' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => env('common.config.params.queue.host'),
            'port' => env('common.config.params.queue.port'),
            'tube' => 'queue_phone_check',
        ],
        'queue_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => env('common.config.params.queue.host'),
            'port' => env('common.config.params.queue.port'),
            'tube' => 'queue_job',
        ],
        'queue_system_services' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => env('common.config.params.queue.host'),
            'port' => env('common.config.params.queue.port'),
            'tube' => 'queue_system_services',
        ],
        'queue_client_chat_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => env('common.config.params.queue.host'),
            'port' => env('common.config.params.queue.port'),
            'tube' => 'queue_client_chat_job',
            'as idAccess' => sales\behaviors\JobIdAccessBehavior::class
        ],
        'queue_virtual_cron' => [
            'class' => \kivork\VirtualCron\Queue\Queue::class,
            'host' => env('common.config.params.queue.host'),
            'port' => env('common.config.params.queue.port'),
            'tube' => 'queue_virtual_cron',
        ],
        'telegram' => [
            'class' => \aki\telegram\Telegram::class,
            'botUsername' => env('common.config.main.components.telegram.botUsername'),
            'botToken' => env('common.config.main.components.telegram.botToken'),
        ],
        'gaRequestService' => [
            'class' => \common\components\ga\GaRequestService::class,
            'url' => env('common.config.main.components.gaRequestService.url'),
        ],
        'centrifugo' => [
            'class'  => \sorokinmedia\centrifugo\Client::class,
            'host'   => env('common.config.main.components.centrifugo.host'),
            'secret' => env('common.config.main.components.centrifugo.secret'),
            'apikey' => env('common.config.main.components.centrifugo.apikey')
        ],
        'prometheus' => [
            'class' => \kivork\PrometheusClient\components\PrometheusClient::class,
            'redisOptions' => [
                'prefix' => php_uname('n'),
                'host' => env('common.config.main.components.prometheus.redisOptions.host'),
                'port' => env('common.config.main.components.prometheus.redisOptions.port'),
                'password' => env('common.config.main.components.prometheus.redisOptions.password'),
                'database' => env('common.config.main.components.prometheus.redisOptions.database', 'int'),
                'timeout' => 0.1,
                'read_timeout' => 10,
                'persistent_connections' => false,
            ],
            'useHttpBasicAuth' => env('common.config.main.components.prometheus.useHttpBasicAuth', 'bool'),
            'authUsername' => env('common.config.main.components.prometheus.authUsername'),
            'authPassword' => env('common.config.main.components.prometheus.authPassword'),
        ],
    ],
];
