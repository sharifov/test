<?php

return [
    'serviceName' => 'app',
    'serviceVersion' => '1.1.0',
    'serviceType' => '',
    'appName' => 'CRM',
    'appHostname' => php_uname('n'),
    'appInstance' => gethostname(),
    'appEnv' => '',

    'release' => require __DIR__ . '/params-release.php',
    'wsIdentityCookie' => ['name' => '_identity_ws', 'httpOnly' => true, 'domain' => parse_url(env('COMMON_CONFIG_PARAMS_URL'), PHP_URL_HOST)],

    'url'      => 'https://sales.travelinsides.com',
    'url_api'  => 'https://sales.api.travelinsides.com/v1',

    'email_from' => [
        'sales' => 'sales@travelinsides.com',
        'no-reply' => 'no-reply@techork.com',
    ],

    'cc_username_prefix' => '',

    'lead' => [
        'call2DelayTime' => 2 * 60 * 60,     // 2 hours
    ],
    'ipinfodb_key' => '',
    'backOffice' => [
        'ver' => '1.0.0',
        'webHookEndpoint' => 'webhook/ping',
        'apiKey' => '',
        'url' => 'https://backoffice.travelinsides.com/api/sync',
        'urlV2' => 'https://backoffice.travelinsides.com/api/v2',
        'urlV3' => 'https://backoffice.travelinsides.com/api/v3',
        'username' => '',
        'password' => '',
    ],

    'telegram' => [
        'webhook_url'   => 'https://sales.api.travelinsides.com/v1/telegram/webhook'
    ],
    'use_browser_call_access' => true,
    'test_phone_list' => [
    ],
    'test_allow_ip_address_list' => [
//      '127.0.0.1'
    ],
    'crypt' => [
        'method'    => 'aes-256-cbc',
        'password'  => '',
        'iv'        => '',
    ],
    'user_voice_mail_alias' => '@frontend/web/',
    'liveChatRealTimeVisitors' => 'https://livechat.travelinsides.com/visitors',
    'price_line_ftp_credential' => [
        'url' => "priceline-reports.travelinsides.com",
        'port' => "22",
        'protocol' => "sftp",
        'path' => "reports",
        'user' => "",
        'pass' => "",
    ],
    'centrifugo' => [
        'enabled' => false,
        'wsConnectionUrl' => 'wss://app.sales.com/centrifugo/connection/websocket',
    ],
    'search' => [
        'host' => '',
        'sid' => '',
        'username' => '',
        'password' => ''
    ],
    'clientChat' => [
        'projectConfig' => [
            'params' => [
                'endpoint' => ''
            ]
        ]
    ],
    'queue' => [
        'host' => 'localhost',
        'port' => 11300,
    ],
    'mattermostLogTarget' => [
        'containerSettings' =>  [
            'driver' => [
                'scheme' => 'https',
                'basePath' => '/api/v4',
                'url' => 'chat.travel-dev.com',
                'login_id' => '',
                'password' => ''
            ],
        ],
        'chanelId' => '',
    ],
    's3' => [
        'credentials' => [
            'key' => '',
            'secret' => '',
        ],
        'region' => 'us-east-1',
        'version' => '2006-03-01',
    ],
    'fileStorage' => [
        'useRemoteStorage' => true,
        'remoteStorage' => [
            'cdn' => [
                'host' => '',
                'prefix' => '',
            ],
            's3' => [
                'bucket' => '',
                'prefix' => '',
                'uploadConfig' => [
                    'visibility' => 'private',// Required: [private or public] for FlySystem -> ACL native S3
                    // League\Flysystem\AwsS3V3\AwsS3V3Adapter\AVAILABLE_OPTIONS
                    'ServerSideEncryption' => 'AES256',
                ],
            ],
        ],
        'localStorage' => [
            'path' => '', // absolute path to server directory
            'url' => '', // full web address: https://sales.test/fs
            'converterConfig' => [
                'fileDir' => [
                    'file' => [
                        'public' => 0644,
                        'private' => 0600,
                    ],
                    'dir' => [
                        'public' => 0755,
                        'private' => 0700,
                    ],
                ],
                'defaultForDirectories' => 'private'
            ],
            'uploadConfig' => [
                'visibility' => 'private',// [private or public] for FlySystem
            ],
        ],
    ],
];
