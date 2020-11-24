<?php

return [
    'serviceName' => '{{ frontend.config.params.serviceName:str }}',
    'limitUserConnections' => '{{ frontend.config.params.limitUserConnections:int }}',   // WebSocket Limit user Connections
    'bsVersion' => '{{ frontend.config.params.bsVersion:str }}',
];
