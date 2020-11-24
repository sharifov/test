<?php

return [
    'components' => [
        'request' => [
            'cookieValidationKey' => '{{ webapi.config.main.components.request.cookieValidationKey:str }}',
        ],
    ],
];
