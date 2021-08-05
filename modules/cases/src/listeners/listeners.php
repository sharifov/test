<?php

use sales\entities\cases\events\CasesCreatedEvent;
use sales\entities\cases\events\CasesFollowUpStatusEvent;
use sales\entities\cases\events\CasesPendingStatusEvent;
use sales\entities\cases\events\CasesProcessingStatusEvent;
use sales\entities\cases\events\CasesSolvedStatusEvent;
use sales\entities\cases\events\CasesTrashStatusEvent;
use sales\entities\cases\events\CasesAwaitingStatusEvent;
use sales\entities\cases\events\CasesAutoProcessingStatusEvent;
use sales\entities\cases\events\CasesErrorStatusEvent;
use sales\listeners\cases\CasesFollowUpStatusEventLogListener;
use sales\listeners\cases\CasesPendingStatusEventLogListener;
use sales\listeners\cases\CasesProcessingStatusEventLogListener;
use sales\listeners\cases\CasesProcessingStatusEventNotificationsListener;
use sales\listeners\cases\CasesSolvedStatusEventLogListener;
use sales\listeners\cases\CasesTrashStatusEventLogListener;
use sales\listeners\cases\CasesAwaitingStatusEventLogListener;
use sales\listeners\cases\CasesAutoProcessingStatusEventListener;
use sales\listeners\cases\CasesErrorStatusEventLogListener;

return [
    CasesCreatedEvent::class => [],
    CasesPendingStatusEvent::class => [CasesPendingStatusEventLogListener::class],
    CasesProcessingStatusEvent::class => [CasesProcessingStatusEventLogListener::class, CasesProcessingStatusEventNotificationsListener::class],
    CasesFollowUpStatusEvent::class => [CasesFollowUpStatusEventLogListener::class],
    CasesSolvedStatusEvent::class => [CasesSolvedStatusEventLogListener::class],
    CasesTrashStatusEvent::class => [CasesTrashStatusEventLogListener::class],
    CasesAwaitingStatusEvent::class => [CasesAwaitingStatusEventLogListener::class],
    CasesAutoProcessingStatusEvent::class => [CasesAutoProcessingStatusEventListener::class],
    CasesErrorStatusEvent::class => [CasesErrorStatusEventLogListener::class],
];
