<?php

use modules\qaTask\src\entities\qaTask\events\QaTaskAssignEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskCanceledEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskClosedEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskDeadlineEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskEscalatedEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskPendingEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskProcessingEvent;
use modules\qaTask\src\entities\qaTask\events\QaTaskUnAssignEvent;

return [
    QaTaskAssignEvent::class => [],
    QaTaskCanceledEvent::class => [],
    QaTaskClosedEvent::class => [],
    QaTaskEscalatedEvent::class => [],
    QaTaskPendingEvent::class => [],
    QaTaskProcessingEvent::class => [],
    QaTaskUnAssignEvent::class => [],
    QaTaskDeadlineEvent::class => [],
];
