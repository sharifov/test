<?php
return [
    'appInstance' => '{{ common.config.params.appInstance:str }}',
    'getAirportUrl' => 'https://backoffice.travelinsides.com/api/v2/airport/search',
    'sync' => [
        'ver' => '1.0.0',
        'apiKey' => '5394bbedf41dd2c0403897ca621f188b',
        'serverUrl' => 'http://bo-dev.zeit.style/api/sync'
    ],
    'AWS_MAILER' => [
        'host' => 'email-smtp.us-east-1.amazonaws.com',
        'port' => '25',
        'security' => 'tls',
        'username' => 'AKIAI5VDT2W5LGW7T3TQ',
        'password' => 'Avufe0iKvYJGNT+Dv8LyVBesiCbMX2ZaB5HC4kBc/2Zn'
    ],
    'searchApiUrl' => 'http://airsearch.api.travelinsides.com/v1/search',
    'crypt' => [
        'method'    => 'aes-256-cbc',
        'password'  => '',
        'iv'        => '',
    ]
];
