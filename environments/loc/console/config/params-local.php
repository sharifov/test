<?php
return [
    'sync' => [
        'ver' => '1.0.0',
        'apiKey' => '5394bbedf41dd2c0403897ca621f188b',
        'serverUrl' => 'https://backoffice.travelinsides.com/api/sync',
        'test' => '{{ console.config.params.sync.test:bool }}',
    ],
    'AWS_MAILER' => [
        'host' => 'email-smtp.us-east-1.amazonaws.com',
        'port' => '25',
        'security' => 'tls',
        'username' => 'AKIAI5VDT2W5LGW7T3TQ',
        'password' => '{{ console.config.params.AWS_MAILER.password:str }}'
    ]
];
