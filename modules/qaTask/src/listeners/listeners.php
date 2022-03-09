<?php

use modules\qaTask\src\entities\qaTask\events\QaTaskAssignEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskCreatedEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskStatusCanceledEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskChangeRatingEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskStatusClosedEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskDeadlineEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskStatusEscalatedEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskStatusPendingEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskStatusProcessingEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskUnAssignEvent;
use modules\qaTask\src\listeners\QaTaskCancelNotifierListener;
use modules\qaTask\src\listeners\QaTaskReturnNotifierListener;
use modules\qaTask\src\listeners\QaTaskTakeOverNotifierListener;
use modules\qaTask\src\useCases\qaTask\cancel\QaTaskCancelEvent;
use modules\qaTask\src\useCases\qaTask\close\QaTaskCloseEvent;
use modules\qaTask\src\useCases\qaTask\create\chat\withoutNewMessages\QaTaskCreateChatWithoutNewMessagesEvent;
use modules\qaTask\src\useCases\qaTask\create\lead\closeCheck\QaTaskCreateLeadCloseCheckEvent;
use modules\qaTask\src\useCases\qaTask\create\lead\processingQuality\QaTaskCreateLeadProcessingQualityEvent;
use modules\qaTask\src\useCases\qaTask\create\lead\trashCheck\QaTaskCreateLeadTrashCheckEvent;
use modules\qaTask\src\useCases\qaTask\create\manually\QaTaskCreateManuallyEvent;
use modules\qaTask\src\useCases\qaTask\decide\QaTaskDecideEvent;
use modules\qaTask\src\useCases\qaTask\escalate\QaTaskEscalateEvent;
use modules\qaTask\src\useCases\qaTask\returnTask\QaTaskReturnEvent;
use modules\qaTask\src\useCases\qaTask\returnTask\toEscalate\QaTaskReturnToEscalateEvent;
use modules\qaTask\src\useCases\qaTask\returnTask\toPending\QaTaskReturnToPendingEvent;
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
    QaTaskCreatedEvent::class => [],

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
    QaTaskCancelEvent::class => [
        QaTaskChangeStateEventListener::class,
        QaTaskCancelNotifierListener::class,
    ],
    QaTaskReturnEvent::class => [
        QaTaskChangeStateEventListener::class,
        QaTaskReturnNotifierListener::class,
    ],
    QaTaskReturnToPendingEvent::class => [],
    QaTaskReturnToEscalateEvent::class => [],
    QaTaskDecideEvent::class => [
        QaTaskChangeStateEventListener::class,
    ],
    QaTaskCreateManuallyEvent::class => [
        QaTaskChangeStateEventListener::class,
    ],
    QaTaskCreateLeadProcessingQualityEvent::class => [
        QaTaskChangeStateEventListener::class,
    ],
    QaTaskCreateLeadTrashCheckEvent::class => [
        QaTaskChangeStateEventListener::class,
    ],
    QaTaskCreateChatWithoutNewMessagesEvent::class => [
        QaTaskChangeStateEventListener::class,
    ],
    QaTaskCreateLeadCloseCheckEvent::class => [
        QaTaskChangeStateEventListener::class,
    ]
];
