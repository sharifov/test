<?php
return [
    'serviceName' => 'crm',
    'serviceVersion' => '1.0.0',
    'appName' => 'Sales',
    'appInstance' => '1',

    'release' => require __DIR__ . '/params-release.php',
    'wsIdentityCookie' => ['name' => '_identity_ws', 'httpOnly' => true],

    'url_address'      => 'https://sales.travelinsides.com',
    'url_api_address'  => 'https://sales.api.travelinsides.com/v1',

    'email_from' => [
        'sales' => 'sales@travelinsides.com',
    ],

    'email_to' => [
        'bcc_sales' => 'supers@wowfare.com'
    ]
    ,
    'syncAirlineClasses' => 'https://airsearch.api.travelinsides.com/airline/get-cabin-classes',
    'search' => [
        'host' => 'https://airsearch.api.travelinsides.com',
        'api_cid' => 'SAL101',
        'api_key' => 'c940e3484fe9fcc73ed12a7fcec469b4'
    ],

    'searchApiUrl' => 'https://airsearch.api.travelinsides.com/v1/search',
    'lead' => [
        'call2DelayTime' => 2 * 60 * 60,     // 2 hours
    ],
    'processing_fee' => 25,

    'ipinfodb_key' => '9079611957f72155dea3bb7ab848ee101c268564ab64921ca5345c4bce7af5b7',

    'backOffice' => [
        'ver' => '1.0.0',
        'apiKey' => '5394bbedf41dd2c0403897ca621f188b',
        'serverUrl' => 'https://backoffice.travelinsides.com/api/sync',
        'webHookEndpoint' => 'webhook/ping',
    ],
    'global_phone' => '+16692011799',

    'telegram' => [
        'bot_username'  => 'CrmKivorkBot',
        'token'         => '817992632:AAE6UXJRqDscAZc9gUBScEpaT_T4zGukdos',
        //'webhook_url'   => 'https://api-sales.dev.travelinsides.com/v1/telegram/webhook'
        'webhook_url'   => 'https://sales.api.travelinsides.com/v1/telegram/webhook'
    ],
    'use_browser_call_access' => true,
	'test_phone_list' => [
	],
	'test_allow_ip_address_list' => [
//		'127.0.0.1'
	],
    'crypt' => [
        'method'    => 'aes-256-cbc',
        'password'  => '',
        'iv'        => '',
    ],
];
