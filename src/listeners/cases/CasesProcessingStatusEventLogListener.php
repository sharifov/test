<?php

namespace src\listeners\cases;

use src\entities\cases\CasesStatus;
use src\entities\cases\events\CasesProcessingStatusEvent;
use src\services\cases\CaseStatusLogService;
use Yii;

/**
 * Class CasesProcessingStatusEventLogListener
 *
 * @property CaseStatusLogService $caseStatusLogService
 */
class CasesProcessingStatusEventLogListener
{
    private $caseStatusLogService;

    public function __construct(CaseStatusLogService $caseStatusLogService)
    {
        $this->caseStatusLogService = $caseStatusLogService;
    }

    public function handle(CasesProcessingStatusEvent $event): void
    {
        try {
            $this->caseStatusLogService->log(
                $event->case->cs_id,
                CasesStatus::STATUS_PROCESSING,
                $event->oldStatus,
                $event->newOwnerId,
                $event->creatorId,
                $event->description
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:CasesProcessingStatusEventLogListener');
        }
    }
}
