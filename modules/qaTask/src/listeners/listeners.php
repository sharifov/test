<?php

use modules\qaTask\src\entities\qaTask\events\QaTaskAssignEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskStatusCanceledEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskChangeRatingEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskStatusClosedEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskDeadlineEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskStatusEscalatedEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskStatusPendingEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskStatusProcessingEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskUnAssignEvent;
use modules\qaTask\src\listeners\QaTaskTakeOverNotifierListener;
use modules\qaTask\src\useCases\qaTask\close\QaTaskCloseEvent;
use modules\qaTask\src\useCases\qaTask\escalate\QaTaskEscalateEvent;
use modules\qaTask\src\useCases\qaTask\take\QaTaskTakeEvent;
use modules\qaTask\src\useCases\qaTask\takeOver\QaTaskTakeOverEvent;
use modules\qaTask\src\listeners\QaTaskChangeStateEventListener;

return [
    // Entity events
    QaTaskAssignEvent::class => [],
    QaTaskStatusCanceledEvent::class => [],
    QaTaskStatusClosedEvent::class => [],
    QaTaskStatusEscalatedEvent::class => [],
    QaTaskStatusPendingEvent::class => [],
    QaTaskStatusProcessingEvent::class => [],
    QaTaskUnAssignEvent::class => [],
    QaTaskDeadlineEvent::class => [],
    QaTaskChangeRatingEvent::class => [],

    // Use cases events
    QaTaskTakeEvent::class => [
        QaTaskChangeStateEventListener::class,
    ],
    QaTaskTakeOverEvent::class => [
        QaTaskChangeStateEventListener::class,
        QaTaskTakeOverNotifierListener::class,
    ],
    QaTaskEscalateEvent::class => [
        QaTaskChangeStateEventListener::class,
    ],
    QaTaskCloseEvent::class => [
        QaTaskChangeStateEventListener::class,
    ],
];
