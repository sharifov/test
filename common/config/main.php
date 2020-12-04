<?php

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone' => 'UTC',
    'components' => [
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
            'hostname' => 'localhost',
            'unixSocket' => null,
            'port' => 6379,
            'database' => 0,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
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
        'mailer2' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => true,
        ],
        'communication' => [
            'class' => \common\components\CommunicationService::class,
            'url' => 'https://communication.api.travelinsides.com/v1/',
            'url2' => 'https://communication.api.travelinsides.com/v2/',
            'username' => 'sales',
            'password' => '',
            'recording_url' => 'https://api.twilio.com/2010-04-01/Accounts/AC10f3c74efba7b492cbd7dca86077736c/Recordings/',
            'voipApiUsername' => 'sales'
        ],
        'airsearch' => [
            'class' => \common\components\AirSearchService::class,
            'url' => 'https://airsearch.api.travelinsides.com/',
            'username' => 'SAL101',
            'password' => 'c940e3484fe9fcc73ed12a7fcec469b4',
        ],
        'currency' => [
            'class' => \common\components\CurrencyService::class,
            'url' => 'https://airsearch.api.travelinsides.com/v1/',
            'username' => 'SAL101',
            'password' => 'c940e3484fe9fcc73ed12a7fcec469b4',
        ],
        'rchat' => [
            'class' => \common\components\RocketChat::class,
            'url' => 'https://rocketchat.travel-dev.com/api/v1/',
            'username' => '',
            'password' => '',
            'host' => 'https://rocketchat.travel-dev.com',
        ],

        'chatBot' => [
            'class' => \common\components\ChatBot::class,
            'url' => 'https://chatbot.travel-dev.com/private/api/v1/',
            'username' => '',
            'password' => '',
        ],

        'travelServices' => [
            'class' => \common\components\TravelServices::class,
            'url' => 'https://geonames.travelinsides.com/api/v1/',
            'username' => '',
            'password' => '',
        ],

        'queue_sms_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => 'localhost',
            'port' => 11300,
            'tube' => 'queue_sms_job',
        ],
        'queue_email_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => 'localhost',
            'port' => 11300,
            'tube' => 'queue_email_job',
        ],
        'queue_phone_check' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => 'localhost',
            'port' => 11300,
            'tube' => 'queue_phone_check',
        ],
        'queue_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => 'localhost',
            'port' => 11300,
            'tube' => 'queue_job',
        ],
        'queue_system_services' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => 'localhost',
            'port' => 11300,
            'tube' => 'queue_system_services',
        ],
        'queue_client_chat_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => 'localhost',
            'port' => 11300,
            'tube' => 'queue_client_chat_job',
            'as idAccess' => sales\behaviors\JobIdAccessBehavior::class
        ],
        'queue_virtual_cron' => [
            'class' => \kivork\VirtualCron\Queue\Queue::class,
            'host' => 'localhost',
            'port' => 11300,
            'tube' => 'queue_virtual_cron',
        ],
        'telegram' => [
            'class' => \aki\telegram\Telegram::class,
            'botUsername' => 'CrmKivorkBot',
            'botToken' => '817992632:AAE6UXJRqDscAZc9gUBScEpaT_T4zGukdos',
        ],
        'gaRequestService' => [
            'class' => \common\components\ga\GaRequestService::class,
            'url' => 'https://www.google-analytics.com/collect',  // For test : debug/collect
        ],
        'prometheus' => [
            'class' => \kivork\PrometheusClient\components\PrometheusClient::class,
            'redisOptions' => [
                'prefix' => php_uname('n'),
                'host' => 'localhost',
                'port' => 6379,
                'password' => null,
                'timeout' => 0.1, // in seconds
                'read_timeout' => 10, // in seconds
                'persistent_connections' => false,
                'database' => 3,
            ],
            'useHttpBasicAuth' => false,
            'authUsername' => '',
            'authPassword' => '',
        ],
        'centrifugo' => [
            'class'  => \sorokinmedia\centrifugo\Client::class,
            'host'   => 'http://localhost:8000/api',
            'secret' => '',
            'apikey' => ''
        ],
    ],
    'bootstrap' => [
        'queue_sms_job',
        'queue_email_job',
        'queue_phone_check',
        'queue_job',
        'queue_client_chat_job',
        'queue_system_services',
        'queue_virtual_cron',
        \common\components\SettingsBootstrap::class,
        common\bootstrap\SetUp::class,
        common\bootstrap\SetUpListeners::class,
        common\bootstrap\Logger::class,
    ],
];
