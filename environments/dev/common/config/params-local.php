<?php
return [
    'serviceName' => '{{ common.config.params.serviceName:str }}',
    'serviceVersion' => '{{ common.config.params.serviceVersion:str }}',
    'appName' => '{{ common.config.params.appName:str }}',
    'appInstance' => '{{ common.config.params.appInstance:str }}',
    'appEnv' => 'dev',

    'url_address'      => '{{ common.config.params.url_address:str }}',
    'url_api_address'  => '{{ common.config.params.url_api_address:str }}',

    'email_from' => [
        'sales' => '{{ common.config.params.email_from.sales:str }}',
        'no-reply' => '{{ common.config.params.email_from.no-reply:str }}',
    ],

    'email_to' => [
        'bcc_sales' => '{{ common.config.params.email_to.bcc_sales:str }}'
    ],
    'lead' => [
        'call2DelayTime' =>'{{ common.config.params.lead.call2DelayTime:int }}',
    ],
    'ipinfodb_key' => '{{ common.config.params.lead.ipinfodb_key }}',

    'backOffice' => [
        'ver' => '{{ common.config.params.backOffice.ver:str }}',
        'apiKey' => '{{ common.config.params.backOffice.apiKey:str }}',
        'serverUrl' => '{{ common.config.params.backOffice.serverUrl:str }}',
        'serverUrlV3' => '{{ common.config.params.backOffice.serverUrlV3:str }}',
        'webHookEndpoint' => 'webhook/ping',
        'username' => '{{ common.config.params.backOffice.username:str }}',
        'password' => '{{ common.config.params.backOffice.password:str }}'
    ],

    'global_phone' => '{{ common.config.params.global_phone:str }}',

    'telegram' => [
        'bot_username'  => '{{ common.config.params.telegram.bot_username:str }}',
        'token'         => '{{ common.config.params.telegram.token:str }}',
        'webhook_url'   => '{{ common.config.params.telegram.webhook_url:str }}',
    ],
    'use_browser_call_access' => '{{ common.config.params.use_browser_call_access:bool }}',

    'test_phone_list' => [
    ],

    'test_allow_ip_address_list' => [
    ],
    'crypt' => [
        'method'    => 'aes-256-cbc',
        'password'  => '{{ common.config.params.crypt.password:str }}',
        'iv'        => '{{ common.config.params.crypt.iv:str }}',
    ],

    'user_voice_mail_alias' => '@frontend/web/',

    'liveChatRealTimeVisitors' => '{{ common.config.params.liveChatRealTimeVisitors:str }}',

    'price_line_ftp_credential' => [
        'url' => '{{ common.config.params.price_line_ftp_credential.url:str }}',
        'port' => '{{ common.config.params.price_line_ftp_credential.port:int }}',
        'protocol' => '{{ common.config.params.price_line_ftp_credential.protocol:str }}',
        'path' => 'reports',
        'user' => '{{ common.config.params.price_line_ftp_credential.user:str }}',
        'pass' => '{{ common.config.params.price_line_ftp_credential.pass:str }}',
    ],

    'getAirportUrl' => '{{ common.config.params.getAirportUrl.url:str }}',

    'searchApiUrl' => '{{ common.config.params.searchApiUrl.url:str }}',

    'centrifugo' => [
        'apiKey' => '{{ common.config.params.centrifugo.apiKey:str }}',
        'jsClientUrl' => '{{ common.config.params.centrifugo.jsClientUrl:str }}',
        'serviceUrl' => '{{ common.config.params.centrifugo.serviceUrl:str }}',
        'tokenHmacSecretKey' => '{{ common.config.params.centrifugo.tokenHmacSecretKey:str }}'
    ],
];
