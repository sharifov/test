<?php

use modules\abac\components\AbacComponent;

return [
    'class' => AbacComponent::class,
    'cacheEnable' => true,
    'modules' => [
        'order' => \modules\order\src\abac\OrderAbacObject::class,
        'case' => \modules\cases\src\abac\CasesAbacObject::class,
        'lead' => \modules\lead\src\abac\LeadAbacObject::class,
        'email' => \modules\email\src\abac\EmailAbacObject::class,
        'qa-task' => \modules\qaTask\src\abac\QaTaskAbacObject::class
    ],
    'scanDirs' => [
        '/modules/',
        '/frontend/',
        '/common/',
        '/sales/',
    ],
    'scanExtMask' => ['*.php'],
];
