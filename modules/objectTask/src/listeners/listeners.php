<?php

use modules\objectTask\src\events\ObjectTaskStatusChangeEvent;
use modules\objectTask\src\listeners\ObjectTaskStatusChangedListener;

return [
    ObjectTaskStatusChangeEvent::class => [
        ObjectTaskStatusChangedListener::class,
    ],
];
