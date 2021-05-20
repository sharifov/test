<?php

use modules\abac\components\AbacComponent;

return [
    'class' => AbacComponent::class,
    'modules' => [
        'order' => \modules\order\src\abac\OrderAbacObject::class
    ],
];
