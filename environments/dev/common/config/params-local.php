<?php

return [
    'appEnv' => 'dev',
    'appInstance' => env('common.config.params.appInstance'),

    'url_address'      => env('common.config.params.url_address'),
    'url_api_address'  => env('common.config.params.url_api_address'),

    'backOffice' => [
        'ver' => '1.0.0',
        'apiKey' => env('common.config.params.backOffice.apiKey'),
        'serverUrl' => env('common.config.params.backOffice.serverUrl'),
        'serverUrlV2' => env('common.config.params.backOffice.serverUrlV2'),
        'serverUrlV3' => env('common.config.params.backOffice.serverUrlV3'),
        'webHookEndpoint' => 'webhook/ping',
        'username' => env('common.config.params.backOffice.username'),
        'password' => env('common.config.params.backOffice.password')
    ],

    'crypt' => [
        'method'    => 'aes-256-cbc',
        'password'  => env('common.config.params.crypt.password'),
        'iv'        => env('common.config.params.crypt.iv'),
    ],

    'cc_username_prefix' => env('common.config.params.cc_username_prefix'),

    'telegram' => [
        'webhook_url'   => env('common.config.params.telegram.webhook_url'),
    ],

    'use_browser_call_access' => env('common.config.params.use_browser_call_access'),

    'liveChatRealTimeVisitors' => env('common.config.params.liveChatRealTimeVisitors'),

    'price_line_ftp_credential' => [
        'url' => env('common.config.params.price_line_ftp_credential.url'),
        'port' => env('common.config.params.price_line_ftp_credential.port'),
        'protocol' => env('common.config.params.price_line_ftp_credential.protocol'),
        'path' => 'reports',
        'user' => env('common.config.params.price_line_ftp_credential.user'),
        'pass' => env('common.config.params.price_line_ftp_credential.pass'),
    ],

    'centrifugo' => [
        'enabled' => env('common.config.params.centrifugo.enabled'),
        'wsConnectionUrl' => env('common.config.params.centrifugo.wsConnectionUrl'),
    ],

    'search' => [
        'host' => env('common.config.params.search.host'),
        'sid' => env('common.config.params.search.sid'),
        'username' => env('common.config.params.search.username'),
        'password' => env('common.config.params.search.password')
    ],

    'clientChat' => [
        'projectConfig' => [
            'params' => [
                'endpoint' => env('common.config.params.clientChat.projectConfig.params.endpoint'),
            ]
        ]
    ],

    's3' => [
        'credentials' => [
            'key' => env('common.config.params.s3.credentials.key'),
            'secret' => env('common.config.params.s3.credentials.secret'),
        ],
        'region' => 'us-east-1',
        'version' => '2006-03-01',
    ],
    'fileStorage' => [
        'useRemoteStorage' => env('common.config.params.fileStorage.useRemoteStorage'),
        'remoteStorage' => [
            'cdn' => [
                'host' => env('common.config.params.fileStorage.remoteStorage.cdn.host'),
                'prefix' => env('common.config.params.fileStorage.remoteStorage.cdn.prefix'),
            ],
            's3' => [
                'bucket' => env('common.config.params.fileStorage.remoteStorage.s3.bucket'),
                'prefix' => env('common.config.params.fileStorage.remoteStorage.s3.prefix'),
                'uploadConfig' => [
                    'visibility' => 'private',// Required: [private or public] for FlySystem -> ACL native S3
                    // League\Flysystem\AwsS3V3\AwsS3V3Adapter\AVAILABLE_OPTIONS
                    'ServerSideEncryption' => 'AES256',
                ],
            ],
        ],
        'localStorage' => [
            'path' => env('common.config.params.fileStorage.localStorage.path'),
            'url' => env('common.config.params.fileStorage.localStorage.url'),
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
];
