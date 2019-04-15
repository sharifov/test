<?php
return [
    /*'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,*/

    'serviceName' => 'communication',
    'serviceVersion' => '1.0.0',


    'appName' => 'Sales',

    'url_address'      => 'https://sales.travelinsides.com',

    'email_from' => [
        'sales' => 'sales@travelinsides.com',
    ],

    'email_to' => [
        'bcc_sales' => 'supers@wowfare.com'
    ]
    ,
    'syncAirlineClasses' => 'https://airsearch.api.travelinsides.com/airline/get-cabin-classes',
    'searchApiUrl' => 'https://airsearch.api.travelinsides.com/v1/search',

    'lead' => [
        'call2DelayTime' => 2 * 60 * 60,     // 2 hours
    ],
    'processing_fee' => 25,

    'ipinfodb_key' => '9079611957f72155dea3bb7ab848ee101c268564ab64921ca5345c4bce7af5b7',

    'backOffice' => [
        'ver' => '1.0.0',
        'apiKey' => '5394bbedf41dd2c0403897ca621f188b',
        'serverUrl' => 'https://backoffice.zeit.style/api/sync'
    ],
    'global_phone' => '+15596489977',

    'telegram' => [
        'bot_username'  => 'CrmKivorkBot',
        'token'         => '817992632:AAE6UXJRqDscAZc9gUBScEpaT_T4zGukdos',
        'webhook_url'   => 'https://api-sales.dev.travelinsides.com/v1/telegram/webhook'

        //'webhook_url'   => 'https://sales.api.travelinsides.com/v1/telegram/webhook'
    ]
];
