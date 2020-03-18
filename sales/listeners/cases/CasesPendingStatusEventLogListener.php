<?php

namespace sales\listeners\cases;

use sales\entities\cases\CasesStatus;
use sales\entities\cases\events\CasesPendingStatusEvent;
use sales\services\cases\CaseStatusLogService;
use Yii;

/**
 * Class CasesPendingStatusEventLogListener
 *
 * @property CaseStatusLogService $caseStatusLogService
 */
class CasesPendingStatusEventLogListener
{
    private $caseStatusLogService;

    public function __construct(CaseStatusLogService $caseStatusLogService)
    {
        $this->caseStatusLogService = $caseStatusLogService;
    }

    public function handle(CasesPendingStatusEvent $event): void
    {
        try {
            $this->caseStatusLogService->log(
                $event->case->cs_id,
                CasesStatus::STATUS_PENDING,
                $event->oldStatus,
                $event->ownerId,
                $event->creatorId,
                $event->description
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:CasesPendingStatusEventLogListener');
        }
    }
}
