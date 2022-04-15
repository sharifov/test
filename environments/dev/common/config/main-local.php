<?php

return [
    'name' => 'CRM - DEV',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . env('COMMON_CONFIG_MAIN_COMPONENTS_DB_DSN_HOST') . ';dbname=' . env('COMMON_CONFIG_MAIN_COMPONENTS_DB_DSN_DBNAME'),
            'username' => env('COMMON_CONFIG_MAIN_COMPONENTS_DB_USERNAME'),
            'password' => env('COMMON_CONFIG_MAIN_COMPONENTS_DB_PASSWORD'),
            'charset' => 'utf8mb4',
            'enableSchemaCache' => env('COMMON_CONFIG_MAIN_COMPONENTS_DB_ENABLESCHEMACACHE', 'bool'),
            'schemaCacheDuration' => env('COMMON_CONFIG_MAIN_COMPONENTS_DB_SCHEMACACHEDURATION', 'int'),
        ],
        'db_postgres' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=' . env('COMMON_CONFIG_MAIN_COMPONENTS_DBPOSTGRES_DSN_HOST') . ';port=' . env('COMMON_CONFIG_MAIN_COMPONENTS_DBPOSTGRES_DSN_PORT') . ';dbname=' . env('COMMON_CONFIG_MAIN_COMPONENTS_DBPOSTGRES_DSN_DBNAME'),
            'username' => env('COMMON_CONFIG_MAIN_COMPONENTS_DBPOSTGRES_USERNAME'),
            'password' => env('COMMON_CONFIG_MAIN_COMPONENTS_DBPOSTGRES_PASSWORD'),
            'charset' => 'utf8',
            'enableSchemaCache' => env('COMMON_CONFIG_MAIN_COMPONENTS_DBPOSTGRES_ENABLESCHEMACACHE', 'bool'),
            'schemaCacheDuration' => env('COMMON_CONFIG_MAIN_COMPONENTS_DBPOSTGRES_SCHEMACACHEDURATION', 'int'),
        ],
        'db_slave' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . env('COMMON_CONFIG_MAIN_COMPONENTS_DBSLAVE_DSN_HOST') . ';dbname=' . env('COMMON_CONFIG_MAIN_COMPONENTS_DBSLAVE_DSN_DBNAME'),
            'username' => env('COMMON_CONFIG_MAIN_COMPONENTS_DBSLAVE_USERNAME'),
            'password' => env('COMMON_CONFIG_MAIN_COMPONENTS_DBSLAVE_PASSWORD'),
            'charset' => 'utf8mb4',
            'enableSchemaCache' => env('COMMON_CONFIG_MAIN_COMPONENTS_DBSLAVE_ENABLESCHEMACACHE', 'bool'),
            'schemaCacheDuration' => env('COMMON_CONFIG_MAIN_COMPONENTS_DBSLAVE_SCHEMACACHEDURATION', 'int'),

            'slaveConfig' => [
                'username' => env('COMMON_CONFIG_MAIN_COMPONENTS_DBSLAVE_SLAVECONFIG_USERNAME'),
                'password' => env('COMMON_CONFIG_MAIN_COMPONENTS_DBSLAVE_SLAVECONFIG_PASSWORD'),
            ],

            'slaves' => [
                ['dsn' => 'mysql:host=' . env('COMMON_CONFIG_MAIN_COMPONENTS_DBSLAVE_SLAVES_0_DSN_HOST') . ';port=3306;dbname=' . env('COMMON_CONFIG_MAIN_COMPONENTS_DBSLAVE_SLAVES_0_DSN_DBNAME'),]
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
            'hostname' => env('COMMON_CONFIG_MAIN_COMPONENTS_REDIS_HOSTNAME'),
            'port' => env('COMMON_CONFIG_MAIN_COMPONENTS_REDIS_PORT'),
            'database' => env('COMMON_CONFIG_MAIN_COMPONENTS_REDIS_DATABASE', 'int'),
            'password' => env('COMMON_CONFIG_MAIN_COMPONENTS_REDIS_PASSWORD'),
            'unixSocket' => null,
        ],
        'session' => [
            'class' => \yii\redis\Session::class,
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => env('COMMON_CONFIG_MAIN_COMPONENTS_CACHE_REDIS_HOSTNAME'),
                'port' => env('COMMON_CONFIG_MAIN_COMPONENTS_CACHE_REDIS_PORT'),
                'password' => env('COMMON_CONFIG_MAIN_COMPONENTS_CACHE_REDIS_PASSWORD'),
                'database' => env('COMMON_CONFIG_MAIN_COMPONENTS_CACHE_REDIS_DATABASE', 'int'),
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
            'useFileTransport' => true,
        ],
        'communication' => [
            'class' => \common\components\CommunicationService::class,
            'host' => env('COMMON_CONFIG_MAIN_COMPONENTS_COMMUNICATION_HOST'),
            'url' => env('COMMON_CONFIG_MAIN_COMPONENTS_COMMUNICATION_URL'),
            'username' => env('COMMON_CONFIG_MAIN_COMPONENTS_COMMUNICATION_USERNAME'),
            'password' => env('COMMON_CONFIG_MAIN_COMPONENTS_COMMUNICATION_PASSWORD'),
            'voipApiUsername' => env('COMMON_CONFIG_MAIN_COMPONENTS_COMMUNICATION_VOIPAPIUSERNAME'),
            'xAccelRedirectCommunicationUrl' => env('COMMON_CONFIG_MAIN_COMPONENTS_COMMUNICATION_XACCELREDIRECTCOMMUNICATIONURL'),
        ],
        'hybrid' => [
            'class' => \common\components\HybridService::class,
            'username' => env('COMMON_CONFIG_MAIN_COMPONENTS_HYBRID_USERNAME'),
            'password' => env('COMMON_CONFIG_MAIN_COMPONENTS_HYBRID_PASSWORD'),
        ],
        'airsearch' => [
            'class' => \common\components\AirSearchService::class,
            'url' => env('COMMON_CONFIG_MAIN_COMPONENTS_AIRSEARCH_URL'),
            'username' => env('COMMON_CONFIG_MAIN_COMPONENTS_AIRSEARCH_USERNAME'),
            'password' => env('COMMON_CONFIG_MAIN_COMPONENTS_AIRSEARCH_PASSWORD'),
            'searchQuoteEndpoint' => 'v1/internalsearch',
            'searchQuoteByKeyEndpoint' => 'v1/result',
        ],
        'currency' => [
            'class' => \common\components\CurrencyService::class,
            'url' => env('COMMON_CONFIG_MAIN_COMPONENTS_CURRENCY_URL'),
            'username' => env('COMMON_CONFIG_MAIN_COMPONENTS_CURRENCY_USERNAME'),
            'password' => env('COMMON_CONFIG_MAIN_COMPONENTS_CURRENCY_PASSWORD'),
        ],
        'rchat' => [
            'class' => \common\components\RocketChat::class,
            'username' => env('COMMON_CONFIG_MAIN_COMPONENTS_RCHAT_USERNAME'),
            'password' => env('COMMON_CONFIG_MAIN_COMPONENTS_RCHAT_PASSWORD'),
            'host' => env('COMMON_CONFIG_MAIN_COMPONENTS_RCHAT_HOST'),
            'url' => env('COMMON_CONFIG_MAIN_COMPONENTS_RCHAT_URL'),
            'apiServer' => env('COMMON_CONFIG_MAIN_COMPONENTS_RCHAT_APISERVER'),
            'chatApiScriptUrl' => env('COMMON_CONFIG_MAIN_COMPONENTS_RCHAT_CHATAPISCRIPTURL')
        ],
        'chatBot' => [
            'class' => \common\components\ChatBot::class,
            'url' => env('COMMON_CONFIG_MAIN_COMPONENTS_CHATBOT_URL'),
            'username' => env('COMMON_CONFIG_MAIN_COMPONENTS_CHATBOT_USERNAME'),
            'password' => env('COMMON_CONFIG_MAIN_COMPONENTS_CHATBOT_PASSWORD'),
        ],
        'travelServices' => [
            'class' => \common\components\TravelServices::class,
            'url' => env('COMMON_CONFIG_MAIN_COMPONENTS_TRAVELSERVICES_URL'),
            'username' => env('COMMON_CONFIG_MAIN_COMPONENTS_TRAVELSERVICES_USERNAME'),
            'password' => env('COMMON_CONFIG_MAIN_COMPONENTS_TRAVELSERVICES_PASSWORD'),
        ],
        'queue_sms_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => env('COMMON_CONFIG_PARAMS_QUEUE_HOST'),
            'port' => env('COMMON_CONFIG_PARAMS_QUEUE_PORT'),
            'tube' => 'queue_sms_job',
        ],
        'queue_email_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => env('COMMON_CONFIG_PARAMS_QUEUE_HOST'),
            'port' => env('COMMON_CONFIG_PARAMS_QUEUE_PORT'),
            'tube' => 'queue_email_job',
        ],
        'queue_phone_check' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => env('COMMON_CONFIG_PARAMS_QUEUE_HOST'),
            'port' => env('COMMON_CONFIG_PARAMS_QUEUE_PORT'),
            'tube' => 'queue_phone_check',
        ],
        'queue_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => env('COMMON_CONFIG_PARAMS_QUEUE_HOST'),
            'port' => env('COMMON_CONFIG_PARAMS_QUEUE_PORT'),
            'tube' => 'queue_job',
        ],
        'queue_system_services' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => env('COMMON_CONFIG_PARAMS_QUEUE_HOST'),
            'port' => env('COMMON_CONFIG_PARAMS_QUEUE_PORT'),
            'tube' => 'queue_system_services',
        ],
        'queue_client_chat_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => env('COMMON_CONFIG_PARAMS_QUEUE_HOST'),
            'port' => env('COMMON_CONFIG_PARAMS_QUEUE_PORT'),
            'tube' => 'queue_client_chat_job',
            'as idAccess' => src\behaviors\JobIdAccessBehavior::class
        ],
        'queue_virtual_cron' => [
            'class' => \kivork\VirtualCron\Queue\Queue::class,
            'host' => env('COMMON_CONFIG_PARAMS_QUEUE_HOST'),
            'port' => env('COMMON_CONFIG_PARAMS_QUEUE_PORT'),
            'tube' => 'queue_virtual_cron',
        ],
        'queue_lead_redial' => [
            'class' => \common\components\queue\beanstalk\QueueMutex::class,
            'host' => env('COMMON_CONFIG_PARAMS_QUEUE_HOST'),
            'port' => env('COMMON_CONFIG_PARAMS_QUEUE_PORT'),
            'tube' => 'queue_lead_redial',
            'mutex' => [
                'class' => \yii\redis\Mutex::class,
                'redis' => 'redis',
                'expire' => 60,
                'retryDelay' => 500,
            ],
        ],
        'telegram' => [
            'class' => \aki\telegram\Telegram::class,
            'botUsername' => env('COMMON_CONFIG_MAIN_COMPONENTS_TELEGRAM_BOTUSERNAME'),
            'botToken' => env('COMMON_CONFIG_MAIN_COMPONENTS_TELEGRAM_BOTTOKEN'),
        ],
        'gaRequestService' => [
            'class' => \common\components\ga\GaRequestService::class,
            'url' => env('COMMON_CONFIG_MAIN_COMPONENTS_GAREQUESTSERVICE_URL'),
        ],
        'centrifugo' => [
            'class'  => \sorokinmedia\centrifugo\Client::class,
            'host'   => env('COMMON_CONFIG_MAIN_COMPONENTS_CENTRIFUGO_HOST'),
            'secret' => env('COMMON_CONFIG_MAIN_COMPONENTS_CENTRIFUGO_SECRET'),
            'apikey' => env('COMMON_CONFIG_MAIN_COMPONENTS_CENTRIFUGO_APIKEY')
        ],
        'prometheus' => [
            'class' => \kivork\PrometheusClient\components\PrometheusClient::class,
            'redisOptions' => [
                'prefix' => php_uname('n'),
                'host' => env('COMMON_CONFIG_MAIN_COMPONENTS_PROMETHEUS_REDISOPTIONS_HOST'),
                'port' => env('COMMON_CONFIG_MAIN_COMPONENTS_PROMETHEUS_REDISOPTIONS_PORT'),
                'password' => env('COMMON_CONFIG_MAIN_COMPONENTS_PROMETHEUS_REDISOPTIONS_PASSWORD'),
                'database' => env('COMMON_CONFIG_MAIN_COMPONENTS_PROMETHEUS_REDISOPTIONS_DATABASE', 'int'),
                'timeout' => 0.1,
                'read_timeout' => 10,
                'persistent_connections' => false,
            ],
            'useHttpBasicAuth' => env('COMMON_CONFIG_MAIN_COMPONENTS_PROMETHEUS_USEHTTPBASICAUTH', 'bool'),
            'authUsername' => env('COMMON_CONFIG_MAIN_COMPONENTS_PROMETHEUS_AUTHUSERNAME'),
            'authPassword' => env('COMMON_CONFIG_MAIN_COMPONENTS_PROMETHEUS_AUTHPASSWORD'),
        ],
        'callAntiSpam' => [
            'class' => \common\components\antispam\CallAntiSpamService::class,
            'host' => env('COMMON_CONFIG_MAIN_COMPONENTS_CALLANTISPAM_HOST'),
            'port' => env('COMMON_CONFIG_MAIN_COMPONENTS_CALLANTISPAM_PORT'),
            'timeout' => env('COMMON_CONFIG_MAIN_COMPONENTS_CALLANTISPAM_TIMEOUT', 'int'),
        ],
    ],
];
