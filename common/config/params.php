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
    'global_phone' => '+16692011799',

    'telegram' => [
        'bot_username'  => 'CrmKivorkBot',
        'token'         => '817992632:AAE6UXJRqDscAZc9gUBScEpaT_T4zGukdos',
        //'webhook_url'   => 'https://api-sales.dev.travelinsides.com/v1/telegram/webhook'
        'webhook_url'   => 'https://sales.api.travelinsides.com/v1/telegram/webhook'
    ],
    'voice_gather' => [
        'use_voice_gather' => false,
        'entry_phrase' => ' Hello, and thank you for calling {{project}}.',
        'entry_voice' => 'Polly.Joanna',
        'entry_language' => 'en-US',
        'error_phrase' => ' No options were selected',
        'hold_play' => 'https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3',
        'languages' => [
            1 => [
                'language' => 'en-US',
                'digit' => 1,
                'voice' => 'Polly.Joanna',
                'say' => ' To continue in English, press one. ',
                'say_step2' => 'To speak with our sales representative, press 1. To reach a Customer Support agent, press 2.',
                'hold_voice' => ' Your call is very important to us.  Please hold, while you are connected to the next available agent. This call will be recorded for quality assurance.',
            ],
            2 => [
                'language' => 'ru-RU',
                'digit' => 1,
                'voice' => 'Polly.Tatyana',
                'say' => ', Для русского языка нажмите, 2. ',
                'say_step2' => ' Для связи с отделом продаж, нажмите, 1. для связи со службой поддержки, нажмите, два. ',
                'hold_voice' => ' Ваш звонок очень  важен для нас.  Пожалуйста, подождите соединения с нашим агентом.',
            ],
        ],
        'communication_voiceStatusCallbackUrl' => 'twilio/voice-status-callback',
        'communication_recordingStatusCallbackUrl' => 'twilio/recording-status-callback',
    ],
    'general_line_call_distribution' => [
        'use_general_line_distribution' => 1,
        'general_line_leads_limit' => 10,
        'general_line_role_priority' => 1,
        'general_line_last_hours' => 12,
        'general_line_user_limit' => 10,
        'direct_agent_user_limit' => 3,
    ],
    'use_browser_call_access' => true,
];
