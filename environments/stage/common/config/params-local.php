<?php

return [
    'appEnv' => 'stage',
    'appInstance' => '{{ common.config.params.appInstance:str }}',

    'url_address'      => '{{ common.config.params.url_address:str }}',
    'url_api_address'  => '{{ common.config.params.url_api_address:str }}',

    'backOffice' => [
        'ver' => '1.0.0',
        'apiKey' => '{{ common.config.params.backOffice.apiKey:str }}',
        'serverUrl' => '{{ common.config.params.backOffice.serverUrl:str }}',
        'serverUrlV2' => '{{ common.config.params.backOffice.serverUrlV2:str }}',
        'serverUrlV3' => '{{ common.config.params.backOffice.serverUrlV3:str }}',
        'webHookEndpoint' => 'webhook/ping',
        'username' => '{{ common.config.params.backOffice.username:str }}',
        'password' => '{{ common.config.params.backOffice.password:str }}'
    ],

    'crypt' => [
        'method'    => 'aes-256-cbc',
        'password'  => '{{ common.config.params.crypt.password:str }}',
        'iv'        => '{{ common.config.params.crypt.iv:str }}',
    ],

    'telegram' => [
        'webhook_url'   => '{{ common.config.params.telegram.webhook_url:str }}',
    ],

    'use_browser_call_access' => '{{ common.config.params.use_browser_call_access:bool }}',

    'liveChatRealTimeVisitors' => '{{ common.config.params.liveChatRealTimeVisitors:str }}',

    'price_line_ftp_credential' => [
        'url' => '{{ common.config.params.price_line_ftp_credential.url:str }}',
        'port' => '{{ common.config.params.price_line_ftp_credential.port:int }}',
        'protocol' => '{{ common.config.params.price_line_ftp_credential.protocol:str }}',
        'path' => 'reports',
        'user' => '{{ common.config.params.price_line_ftp_credential.user:str }}',
        'pass' => '{{ common.config.params.price_line_ftp_credential.pass:str }}',
    ],

    'centrifugo' => [
        'enabled' => '{{ common.config.params.centrifugo.enabled:bool }}',
        'wsConnectionUrl' => '{{ common.config.params.centrifugo.wsConnectionUrl:str }}',
    ],

    'clientChat' => [
        'projectConfig' => [
            'params' => [
                'endpoint' => '{{ common.config.params.clientChat.projectConfig.params.endpoint:str }}',
            ]
        ]
    ],
];
