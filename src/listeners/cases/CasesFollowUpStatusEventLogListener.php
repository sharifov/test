<?php

namespace src\listeners\cases;

use src\entities\cases\CasesStatus;
use src\entities\cases\events\CasesFollowUpStatusEvent;
use src\services\cases\CaseStatusLogService;
use Yii;

/**
 * Class CasesFollowUpStatusEventLogListener
 *
 * @property CaseStatusLogService $caseStatusLogService
 */
class CasesFollowUpStatusEventLogListener
{
    private $caseStatusLogService;

    public function __construct(CaseStatusLogService $caseStatusLogService)
    {
        $this->caseStatusLogService = $caseStatusLogService;
    }

    public function handle(CasesFollowUpStatusEvent $event): void
    {
        try {
            $this->caseStatusLogService->log(
                $event->case->cs_id,
                CasesStatus::STATUS_FOLLOW_UP,
                $event->oldStatus,
                null,
                $event->creatorId,
                $event->description
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:CasesFollowUpStatusEventLogListener');
        }
    }
}
