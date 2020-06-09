<?php
return [
    'components' => [
        'request' => [
            'cookieValidationKey' => '{{ frontend.config.main.components.request.cookieValidationKey:str }}',
        ],
    ],
];
