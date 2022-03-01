<?php

use modules\lead\src\events\LeadEvents;

return [
    'class' => \modules\eventManager\components\EventManagerComponent::class,
    'cacheEnable' => true,
    'objectList' => [
        LeadEvents::class => LeadEvents::getName(),
    ],
    'categoryList' => [
        'lead'  => 'Lead',
    ],
];
