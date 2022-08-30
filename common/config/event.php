<?php

use modules\app\src\events\AppEvents;
use modules\lead\src\events\LeadEvents;
use modules\user\src\events\UserEvents;

return [
    'class' => \modules\eventManager\components\EventManagerComponent::class,
    'cacheEnable' => true,
    'objectList' => [
        LeadEvents::class => LeadEvents::getName(),
        AppEvents::class => AppEvents::getName(),
        UserEvents::class => UserEvents::getName(),
    ],
    'categoryList' => [
        'lead'  => 'Lead',
        'app'  => 'App',
        'user'  => 'User',
    ],
];
