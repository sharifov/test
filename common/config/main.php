<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone' => 'UTC',
    'components' => [
        /*'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                ]
            ],
        ],*/
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
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'cacheFile' => [
            'class' => 'yii\caching\FileCache',
            'defaultDuration' => 10 * 60,
            'gcProbability' => 100,
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
            'recording_url' => 'https://api.twilio.com/2010-04-01/Accounts/AC10f3c74efba7b492cbd7dca86077736c/Recordings/'
        ],

        'currency' => [
            'class' => \common\components\CurrencyService::class,
            'url' => 'https://airsearch.api.travelinsides.com/v1/',
            'username' => 'crm',
            'password' => '',
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
        'queue_gmail_download' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => 'localhost',
            'port' => 11300,
            'tube' => 'queue_gmail_download',
        ],

        'telegram' => [
            'class' => \aki\telegram\Telegram::class,
            'botToken' => '817992632:AAE6UXJRqDscAZc9gUBScEpaT_T4zGukdos',
        ]

    ],
    'bootstrap' => [
        'queue_email_job',
        'queue_phone_check',
        'queue_job',
        'queue_gmail_download',
        \common\components\SettingsBootstrap::class,
        common\bootstrap\SetUp::class,
        common\bootstrap\SetUpListeners::class,
        common\bootstrap\Logger::class,
    ],
];
