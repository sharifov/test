<?php

namespace src\listeners\cases;

use src\entities\cases\CasesStatus;
use src\entities\cases\events\CasesAwaitingStatusEvent;
use src\services\cases\CaseStatusLogService;
use Yii;

/**
 * Class CasesAwaitingStatusEventLogListener
 *
 * @property CaseStatusLogService $caseStatusLogService
 */
class CasesAwaitingStatusEventLogListener
{
    private $caseStatusLogService;

    public function __construct(CaseStatusLogService $caseStatusLogService)
    {
        $this->caseStatusLogService = $caseStatusLogService;
    }

    public function handle(CasesAwaitingStatusEvent $event): void
    {
        try {
            $this->caseStatusLogService->log(
                $event->case->cs_id,
                CasesStatus::STATUS_AWAITING,
                $event->oldStatus,
                $event->ownerId,
                $event->creatorId,
                $event->description
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:CasesAwaitingStatusEventLogListener');
        }
    }
}
