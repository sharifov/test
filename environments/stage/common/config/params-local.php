<?php

return [
    'appEnv' => 'stage',
    'appInstance' => '{{ common.config.params.appInstance:str }}',
    'url_api_address'      => '{{ common.config.params.url_api_address:str }}',
    'url_address'      => '{{ common.config.params.url_address:str }}',
    'email_from' => [
        'sales' => '{{ common.config.params.email_from.sales:str }}',
    ],
    'email_to' => [
        'bcc_sales' => '{{ common.config.params.email_to.bcc_sales:str }}'
    ],
    'getAirportUrl' => '{{ common.config.params.getAirportUrl:str }}',
    'sync' => [
        'ver' => '1.0.0',
        'apiKey' => '{{ common.config.params.sync.apiKey:str }}',
        'serverUrl' => '{{ common.config.params.sync.serverUrl:str }}'
    ],
    'AWS_MAILER' => [
        'host' => '{{ common.config.params.AWS_MAILER.host:str }}',
        'port' => '587',
        'security' => 'tls',
        'username' => '{{ common.config.params.AWS_MAILER.username:str }}',
        'password' => '{{ common.config.params.AWS_MAILER.password:str }}'
    ],
    'searchApiUrl' => '{{ common.config.params.searchApiUrl:str }}',
    'crypt' => [
        'method'    => 'aes-256-cbc',
        'password'  => '{{ common.config.params.crypt.password:str }}',
        'iv'        => '{{ common.config.params.crypt.iv:str }}',
    ],
    'liveChatRealTimeVisitors' => '{{ common.config.params.liveChatRealTimeVisitors:str }}',
    'centrifugo' => [
        'enabled' => '{{ common.config.params.centrifugo.enabled:bool }}',
        'wsConnectionUrl' => '{{ common.config.params.centrifugo.wsConnectionUrl:str }}',
    ],
    'search' => [
        'host' => '{{ common.config.params.search.host:str }}',
        'api_cid' => '{{ common.config.params.search.api_cid:str }}',
        'api_key' => '{{ common.config.params.search.api_key:str }}'
    ],
    'backOffice' => [
        'ver' => '{{ common.config.params.backOffice.ver:str }}',
        'apiKey' => '{{ common.config.params.backOffice.apiKey:str }}',
        'serverUrl' => '{{ common.config.params.backOffice.serverUrl:str }}',
        'webHookEndpoint' => 'webhook/ping',
    ],

    'clientChat' => [
        'projectConfig' => [
            'params' => [
                'endpoint' => 'https://stage-livechat.travel-dev.com'
            ]
        ]
    ]

];
