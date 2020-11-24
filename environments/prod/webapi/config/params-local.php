<?php

return [

    'serviceName' => '{{ webapi.config.params.serviceName:str }}',
    'client.passwordResetTokenExpire'  => '{{ webapi.config.params.client.passwordResetTokenExpire:int }}',
    'host' => '{{ webapi.config.params.host:str }}',

    'bo' => [
        'url' => '{{ webapi.config.params.bo.url:str }}',
    ]
];
