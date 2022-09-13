<?php

namespace modules\objectTask\src\listeners;

use modules\objectTask\src\events\ObjectTaskStatusChangeEvent;
use modules\objectTask\src\services\ObjectTaskStatusLogService;

class ObjectTaskStatusChangedListener
{
    public function handle(ObjectTaskStatusChangeEvent $event): void
    {
        ObjectTaskStatusLogService::createLog(
            $event->objectTaskUuid,
            $event->newStatusId,
            $event->oldStatusId,
            $event->description
        );
    }
}
