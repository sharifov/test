<?php
return [
    /*'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,*/



    'appName' => 'KIVORK - Sales',
    'checkIpURL' => 'http://timezoneapi.io/api/ip/?',

    'url_address'      => 'https://sales.travelinsides.com',

    'email_from' => [
        'sales' => 'sales@travelinsides.com',
    ],

    'email_to' => [
        'bcc_sales' => 'supers@wowfare.com'
    ]
    ,
    'syncAirlineClasses' => 'http://airsearch.api.travelinsides.com/airline/get-cabin-classes',
    'searchApiUrl' => 'http://airsearch.api.travelinsides.com/v1/search',

    'lead' => [
        'call2DelayTime' => 2 * 60 * 60,     // 2 hours
    ]

];
