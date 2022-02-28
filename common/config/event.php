<?php

return [
    'class' => \modules\eventManager\components\EventManagerComponent::class,
    'cacheEnable' => true,
    'objectList' => [
        'lead'  => \modules\lead\src\events\LeadEvents::class,
    ],
];
