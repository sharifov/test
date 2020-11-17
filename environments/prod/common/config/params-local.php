<?php
return [

    'serviceName' => '{{ common.config.params.serviceName:str }}',
    'serviceVersion' => '{{ common.config.params.serviceVersion:str }}',
    'appName' => '{{ common.config.params.appName:str }}',
    'appInstance' => '{{ common.config.params.appInstance:str }}',
    'appEnv' => 'prod',

    'url_address'      => '{{ common.config.params.url_address:str }}',
    'url_api_address'  => '{{ common.config.params.url_api_address:str }}',

    'syncAirlineClasses' => '{{ common.config.params.syncAirlineClasses:str }}',
    'getAirportUrl' => '{{ common.config.params.getAirportUrl:str }}',

    'search' => [
        'host' => '{{ common.config.params.search.host:str }}',
        'api_cid' => '{{ common.config.params.search.api_cid:str }}',
        'api_key' => '{{ common.config.params.search.api_key:str }}',
    ],

    'searchApiUrl' => '{{ common.config.params.searchApiUrl:str }}',
    'ipinfodb_key' => '{{ common.config.params.ipinfodb_key:str }}',

    'backOffice' => [
        'ver' => '{{ common.config.params.backOffice.ver:str }}',
        'apiKey' => '{{ common.config.params.backOffice.apiKey:str }}',
        'serverUrl' => '{{ common.config.params.backOffice.serverUrl:str }}',
        'serverUrlV3' => '{{ common.config.params.backOffice.serverUrlV3:str }}',
        'webHookEndpoint' => 'webhook/ping',
    ],

    'telegram' => [
        'bot_username'  => '{{ common.config.params.telegram.bot_username:str }}',
        'token'         => '{{ common.config.params.telegram.token:str }}',
        'webhook_url'   => '{{ common.config.params.telegram.webhook_url:str }}',
    ],

    'crypt' => [
        'method'    => 'aes-256-cbc',
        'password'  => '{{ common.config.params.crypt.password:str }}',
        'iv'        => '{{ common.config.params.crypt.iv:str }}',
    ],

    'email_from' => [
        'sales' => '{{ common.config.params.email_from.sales:str }}',
    ],

    'email_to' => [
        'bcc_sales' => '{{ common.config.params.email_to.bcc_sales:str }}',
    ],

    'lead' => [
        'call2DelayTime' => '{{ common.config.params.lead.call2DelayTime:int }}',
    ],
    'global_phone' => '{{ common.config.params.global_phone:str }}',

    'liveChatRealTimeVisitors' => '{{ common.config.params.liveChatRealTimeVisitors:str }}',

    'centrifugo' => [
        'apiKey' => '{{ common.config.params.centrifugo.apiKey:str }}',
        'jsClientUrl' => '{{ common.config.params.centrifugo.jsClientUrl:str }}',
        'serviceUrl' => '{{ common.config.params.centrifugo.serviceUrl:str }}',
        'tokenHmacSecretKey' => '{{ common.config.params.centrifugo.tokenHmacSecretKey:str }}'
    ],
];
