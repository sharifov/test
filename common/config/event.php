<?php

use modules\lead\src\events\LeadEvents;

return [
    'class' => \modules\eventManager\components\EventManagerComponent::class,
    'cacheEnable' => true,
    'objectList' => [
        LeadEvents::getName()  => LeadEvents::class,
    ],
];
