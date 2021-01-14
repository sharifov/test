<?php

return [
    'serviceName' => 'crm',
    'serviceVersion' => '1.0.0',
    'appName' => 'Sales',
    'appHostname' => php_uname('n'),
    'appInstance' => '1',
    'appEnv' => '',

    'release' => require __DIR__ . '/params-release.php',
    'wsIdentityCookie' => ['name' => '_identity_ws', 'httpOnly' => true],

    'url_address'      => 'https://sales.travelinsides.com',
    'url_api_address'  => 'https://sales.api.travelinsides.com/v1',

    'email_from' => [
        'sales' => 'sales@travelinsides.com',
        'no-reply' => 'no-reply@techork.com',
    ],

    'lead' => [
        'call2DelayTime' => 2 * 60 * 60,     // 2 hours
    ],
    'ipinfodb_key' => '',
    'backOffice' => [
        'ver' => '1.0.0',
        'webHookEndpoint' => 'webhook/ping',
        'apiKey' => '',
        'serverUrl' => 'https://backoffice.travelinsides.com/api/sync',
        'serverUrlV2' => 'https://backoffice.travelinsides.com/api/v2',
        'serverUrlV3' => 'https://backoffice.travelinsides.com/api/v3',
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
];
