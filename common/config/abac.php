<?php

use modules\abac\components\AbacComponent;

return [
    'class' => AbacComponent::class,
    'cacheEnable' => true,
    'modules' => [
        'order' => \modules\order\src\abac\OrderAbacObject::class
    ],
    'scanDirs' => [
        '@root/modules/',
        '@root/frontend/',
        '@root/common/',
        '@root/sales/',
    ],
    'scanExtMask' => ['*.php'],
];
