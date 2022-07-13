<?php

return [
    'appEnv' => 'loc',
    // 'appInstance' => env('COMMON_CONFIG_PARAMS_APPINSTANCE'),

    'url'      => env('COMMON_CONFIG_PARAMS_URL'),
    'url_api'  => env('COMMON_CONFIG_PARAMS_URLAPI'),

    'backOffice' => [
        'ver' => '1.0.0',
        'apiKey' => env('COMMON_CONFIG_PARAMS_BACKOFFICE_APIKEY'),
        'url' => env('COMMON_CONFIG_PARAMS_BACKOFFICE_URL'),
        'urlV2' => env('COMMON_CONFIG_PARAMS_BACKOFFICE_URLV2'),
        'urlV3' => env('COMMON_CONFIG_PARAMS_BACKOFFICE_URLV3'),
        'webHookEndpoint' => 'webhook/ping',
        'username' => env('COMMON_CONFIG_PARAMS_BACKOFFICE_USERNAME'),
        'password' => env('COMMON_CONFIG_PARAMS_BACKOFFICE_PASSWORD')
    ],

    'crypt' => [
        'method'    => 'aes-256-cbc',
        'password'  => env('COMMON_CONFIG_PARAMS_CRYPT_PASSWORD'),
        'iv'        => env('COMMON_CONFIG_PARAMS_CRYPT_IV'),
    ],

    'cc_username_prefix' => env('COMMON_CONFIG_PARAMS_CCUSERNAMEPREFIX'),

    'telegram' => [
        'webhook_url'   => env('COMMON_CONFIG_PARAMS_TELEGRAM_WEBHOOKURL'),
    ],

    'use_browser_call_access' => env('COMMON_CONFIG_PARAMS_USEBROWSERCALLACCESS', 'bool'),

    'liveChatRealTimeVisitors' => env('COMMON_CONFIG_PARAMS_LIVECHATREALTIMEVISITORS'),

    'price_line_ftp_credential' => [
        'url' => env('COMMON_CONFIG_PARAMS_PRICELINEFTPCREDENTIAL_URL'),
        'port' => env('COMMON_CONFIG_PARAMS_PRICELINEFTPCREDENTIAL_PORT'),
        'protocol' => env('COMMON_CONFIG_PARAMS_PRICELINEFTPCREDENTIAL_PROTOCOL'),
        'path' => 'reports',
        'user' => env('COMMON_CONFIG_PARAMS_PRICELINEFTPCREDENTIAL_USER'),
        'pass' => env('COMMON_CONFIG_PARAMS_PRICELINEFTPCREDENTIAL_PASS'),
    ],

    'centrifugo' => [
        'enabled' => env('COMMON_CONFIG_PARAMS_CENTRIFUGO_ENABLED', 'bool'),
        'wsConnectionUrl' => env('COMMON_CONFIG_PARAMS_CENTRIFUGO_WSCONNECTIONURL'),
    ],

    'search' => [
        'host' => env('COMMON_CONFIG_PARAMS_SEARCH_HOST'),
        'sid' => env('COMMON_CONFIG_PARAMS_SEARCH_SID'),
        'username' => env('COMMON_CONFIG_PARAMS_SEARCH_USERNAME'),
        'password' => env('COMMON_CONFIG_PARAMS_SEARCH_PASSWORD')
    ],

    'clientChat' => [
        'projectConfig' => [
            'params' => [
                'endpoint' => env('COMMON_CONFIG_PARAMS_CLIENTCHAT_PROJECTCONFIG_PARAMS_ENDPOINT'),
            ]
        ]
    ],

    's3' => [
        'credentials' => [
            'key' => env('COMMON_CONFIG_PARAMS_S3_CREDENTIALS_KEY'),
            'secret' => env('COMMON_CONFIG_PARAMS_S3_CREDENTIALS_SECRET'),
        ],
        'region' => 'us-east-1',
        'version' => '2006-03-01',
    ],
    'fileStorage' => [
        'useRemoteStorage' => env('COMMON_CONFIG_PARAMS_FILESTORAGE_USEREMOTESTORAGE', 'bool'),
        'remoteStorage' => [
            'cdn' => [
                'host' => env('COMMON_CONFIG_PARAMS_FILESTORAGE_REMOTESTORAGE_CDN_HOST'),
                'prefix' => env('COMMON_CONFIG_PARAMS_FILESTORAGE_REMOTESTORAGE_CDN_PREFIX'),
            ],
            's3' => [
                'bucket' => env('COMMON_CONFIG_PARAMS_FILESTORAGE_REMOTESTORAGE_S3_BUCKET'),
                'prefix' => env('COMMON_CONFIG_PARAMS_FILESTORAGE_REMOTESTORAGE_S3_PREFIX'),
                'uploadConfig' => [
                    'visibility' => 'private',// Required: [private or public] for FlySystem -> ACL native S3
                    // League\Flysystem\AwsS3V3\AwsS3V3Adapter\AVAILABLE_OPTIONS
                    'ServerSideEncryption' => 'AES256',
                ],
            ],
        ],
        'localStorage' => [
            'path' => env('COMMON_CONFIG_PARAMS_FILESTORAGE_LOCALSTORAGE_PATH'),
            'url' => env('COMMON_CONFIG_PARAMS_FILESTORAGE_LOCALSTORAGE_URL'),
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
        ]
    ],
    'ipinfodb_key' => env('COMMON_CONFIG_PARAMS_IPINFODBKEY')
];
