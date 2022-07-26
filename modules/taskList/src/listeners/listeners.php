<?php

use modules\taskList\src\events\UserTaskStatusChangedEvent;
use modules\taskList\src\listeners\UserTaskStatusChangedListener;

return [
    UserTaskStatusChangedEvent::class => [
        UserTaskStatusChangedListener::class
    ],
];
