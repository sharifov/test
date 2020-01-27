<?php

use sales\entities\cases\events\CasesCreatedEvent;
use sales\entities\cases\events\CasesFollowUpStatusEvent;
use sales\entities\cases\events\CasesPendingStatusEvent;
use sales\entities\cases\events\CasesProcessingStatusEvent;
use sales\entities\cases\events\CasesSolvedStatusEvent;
use sales\entities\cases\events\CasesTrashStatusEvent;
use sales\listeners\cases\CasesFollowUpStatusEventLogListener;
use sales\listeners\cases\CasesPendingStatusEventLogListener;
use sales\listeners\cases\CasesProcessingStatusEventLogListener;
use sales\listeners\cases\CasesSolvedStatusEventLogListener;
use sales\listeners\cases\CasesTrashStatusEventLogListener;

return [
    CasesCreatedEvent::class => [],
    CasesPendingStatusEvent::class => [CasesPendingStatusEventLogListener::class],
    CasesProcessingStatusEvent::class => [CasesProcessingStatusEventLogListener::class],
    CasesFollowUpStatusEvent::class => [CasesFollowUpStatusEventLogListener::class],
    CasesSolvedStatusEvent::class => [CasesSolvedStatusEventLogListener::class],
    CasesTrashStatusEvent::class => [CasesTrashStatusEventLogListener::class],
];
