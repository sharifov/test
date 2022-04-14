<?php

use src\entities\cases\events\CasesCreatedEvent;
use src\entities\cases\events\CasesFollowUpStatusEvent;
use src\entities\cases\events\CasesNewStatusEvent;
use src\entities\cases\events\CasesPendingStatusEvent;
use src\entities\cases\events\CasesProcessingStatusEvent;
use src\entities\cases\events\CasesSolvedStatusEvent;
use src\entities\cases\events\CasesTrashStatusEvent;
use src\entities\cases\events\CasesAwaitingStatusEvent;
use src\entities\cases\events\CasesAutoProcessingStatusEvent;
use src\entities\cases\events\CasesErrorStatusEvent;
use src\entities\cases\events\CasesUpdatedInfoEvent;
use src\entities\cases\events\CasesBookingIdChangeEvent;
use src\listeners\cases\CasesFollowUpStatusEventLogListener;
use src\listeners\cases\CasesNewStatusEventLogListener;
use src\listeners\cases\CasesPendingStatusEventLogListener;
use src\listeners\cases\CasesProcessingStatusEventLogListener;
use src\listeners\cases\CasesProcessingStatusEventNotificationsListener;
use src\listeners\cases\CasesSolvedStatusEventLogListener;
use src\listeners\cases\CasesTrashStatusEventLogListener;
use src\listeners\cases\CasesAwaitingStatusEventLogListener;
use src\listeners\cases\CasesAutoProcessingStatusEventListener;
use src\listeners\cases\CasesErrorStatusEventLogListener;
use src\listeners\cases\CasesSwitchStatusAwaitingtoSolvedListener;
use src\listeners\cases\CasesSwitchStatusAwaitingtoErrorListener;
use src\listeners\cases\CasesCreateEventLogListener;
use src\listeners\cases\CasesUpdatedInfoEventLogListener;
use src\listeners\cases\CasesBookingIdChangeEventLogListener;

return [
    CasesCreatedEvent::class => [CasesCreateEventLogListener::class],
    CasesPendingStatusEvent::class => [CasesPendingStatusEventLogListener::class],
    CasesProcessingStatusEvent::class => [CasesProcessingStatusEventLogListener::class, CasesProcessingStatusEventNotificationsListener::class],
    CasesFollowUpStatusEvent::class => [CasesFollowUpStatusEventLogListener::class],
    CasesSolvedStatusEvent::class => [ CasesSolvedStatusEventLogListener::class, CasesSwitchStatusAwaitingtoSolvedListener::class ],
    CasesTrashStatusEvent::class => [CasesTrashStatusEventLogListener::class],
    CasesNewStatusEvent::class => [CasesNewStatusEventLogListener::class],
    CasesAwaitingStatusEvent::class => [CasesAwaitingStatusEventLogListener::class],
    CasesAutoProcessingStatusEvent::class => [CasesAutoProcessingStatusEventListener::class],
    CasesErrorStatusEvent::class => [CasesErrorStatusEventLogListener::class, CasesSwitchStatusAwaitingtoErrorListener::class],
    CasesUpdatedInfoEvent::class => [CasesUpdatedInfoEventLogListener::class],
    CasesBookingIdChangeEvent::class => [CasesBookingIdChangeEventLogListener::class],
];
