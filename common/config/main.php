<?php

use common\components\logger\FilebeatTarget;
use common\helpers\LogHelper;
use common\components\ApplicationStatus;

$commonParams = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php'
);

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone' => 'UTC',
    'components' => [
        'applicationStatus' => [
            'class' => ApplicationStatus::class
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => '',
            'username' => '',
            'password' => '',
            'charset' => 'utf8mb4',
        ],
        'db_slave' => [
            'class' => 'yii\db\Connection',
            'dsn' => '',
            'username' => '',
            'password' => '',
            'charset' => 'utf8mb4',

            'slaveConfig' => [
                'username' => '',
                'password' => ''
            ],

            'slaves' => []
        ],
        'db_postgres' => [
            'class' => 'yii\db\Connection',
            'dsn' => '',
            'username' => '',
            'password' => '',
            'charset' => '',
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
        'log' => [
            'targets' => [
                'analytics-fb-log' => [
                    'class' => FilebeatTarget::class,
                    'levels' => ['info'],
                    'categories' => ['analytics\*', 'AS\*'],
                    'logVars' => [],
                    'prefix' => static function () {
                        return LogHelper::getAnalyticPrefixData();
                    },
                    'logFile' => '@runtime/logs/stash.log'
                ],
            ],
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
            'password' => null,
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => 'localhost',
                'port' => 6379,
                'password' => null,
                'unixSocket' => null,
                'database' => 1,
            ],
        ],
        'cacheFile' => [
            'class' => 'yii\caching\FileCache',
            'defaultDuration' => 10 * 60,
            'gcProbability' => 100,
            'cachePath' => '@console/runtime/cache'
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
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => '',
                'port' => '',
                'username' => '',
                'password' => '',
                'encryption' => '',
            ],
        ],
        'email' => [
            'class' => 'common\components\email\EmailComponent',
            'defaultFromEmail' => '',
        ],
        'communication' => [
            'class' => \common\components\CommunicationService::class,
            'url' => 'https://communication.api.travelinsides.com/v1/',
            'url2' => 'https://communication.api.travelinsides.com/v2/',
            'username' => 'sales',
            'password' => '',
            'xAccelRedirectCommunicationUrl' => '',
            'voipApiUsername' => 'sales',
            'host' => ''
        ],
        'hybrid' => [
            'class' => \common\components\HybridService::class,
            'username' => '',
            'password' => '',
        ],
        'airsearch' => [
            'class' => \common\components\AirSearchService::class,
            'url' => 'https://airsearch.api.travelinsides.com/',
            'username' => '',
            'password' => '',
            'searchQuoteEndpoint' => 'v1/internalsearch',
            'searchQuoteByKeyEndpoint' => 'v1/result',
        ],
        'currency' => [
            'class' => \common\components\CurrencyService::class,
            'url' => 'https://airsearch.api.travelinsides.com/v1/',
            'username' => '',
            'password' => '',
        ],
        'rchat' => [
            'class' => \common\components\RocketChat::class,
            'username' => '',
            'password' => '',
            'host' => 'https://rocketchat.travel-dev.com',
            'apiServer' => 'https://chatbot.travel-dev.com',
            'chatApiScriptUrl' => 'https://cdn.travelinsides.com/npmstatic/chatapi.min.js'
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
            'host' => $commonParams['queue']['host'],
            'port' => $commonParams['queue']['port'],
            'tube' => 'queue_sms_job',
        ],
        'queue_email_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => $commonParams['queue']['host'],
            'port' => $commonParams['queue']['port'],
            'tube' => 'queue_email_job',
        ],
        'queue_phone_check' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => $commonParams['queue']['host'],
            'port' => $commonParams['queue']['port'],
            'tube' => 'queue_phone_check',
        ],
        'queue_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => $commonParams['queue']['host'],
            'port' => $commonParams['queue']['port'],
            'tube' => 'queue_job',
        ],
        'queue_system_services' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => $commonParams['queue']['host'],
            'port' => $commonParams['queue']['port'],
            'tube' => 'queue_system_services',
        ],
        'queue_client_chat_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => $commonParams['queue']['host'],
            'port' => $commonParams['queue']['port'],
            'tube' => 'queue_client_chat_job',
            'as idAccess' => src\behaviors\JobIdAccessBehavior::class
        ],
        'queue_virtual_cron' => [
            'class' => \kivork\VirtualCron\Queue\Queue::class,
            'host' => $commonParams['queue']['host'],
            'port' => $commonParams['queue']['port'],
            'tube' => 'queue_virtual_cron',
        ],
        'queue_lead_redial' => [
            'class' => \common\components\queue\beanstalk\QueueMutex::class,
            'host' => $commonParams['queue']['host'],
            'port' => $commonParams['queue']['port'],
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
            'botUsername' => '',
            'botToken' => '',
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
            'apikey' => '',
        ],
        'abac' => require __DIR__ . '/abac.php',
        'objectSegment' => require __DIR__ . '/objectSegment.php',
        'event' => require __DIR__ . '/event.php',
        'featureFlag' => require __DIR__ . '/featureFlag.php',
        'snowplow' => [
            'class' => \common\components\SnowplowService::class,
            'collectorUrl' => 'sp.ovago.com',
            'appId' => 'crm-app',
            'enabled' => true,
        ],
        'callAntiSpam' => [
            'class' => \common\components\antispam\CallAntiSpamService::class,
            'host' => 'http://localhost',
            'port' => 8001
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
        'queue_lead_redial',
        \common\components\SettingsBootstrap::class,
        \common\bootstrap\SetUp::class,
        \common\bootstrap\SetUpListeners::class,
        \common\bootstrap\Logger::class,
        \common\bootstrap\DeleteLogger::class,
        \common\bootstrap\FileStorage::class,
        \common\bootstrap\PaymentSetup::class,
        \common\bootstrap\OrderProcessManagerQueue::class,
        \modules\order\bootstrap\Logger::class,
        \common\bootstrap\FlightQuoteReprotectionDecisionSetup::class,
        \common\bootstrap\LeadRedialSetUp::class,
        \modules\shiftSchedule\bootstrap\Logger::class
    ],
];
