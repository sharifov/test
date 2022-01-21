<?php

namespace src\listeners\cases;

use src\entities\cases\CasesStatus;
use src\entities\cases\events\CasesErrorStatusEvent;
use src\services\cases\CaseStatusLogService;

/**
 * Class CasesErrorStatusEventLogListener
 *
 * @property CaseStatusLogService $caseStatusLogService
 */
class CasesErrorStatusEventLogListener
{
    private $caseStatusLogService;

    public function __construct(CaseStatusLogService $caseStatusLogService)
    {
        $this->caseStatusLogService = $caseStatusLogService;
    }

    public function handle(CasesErrorStatusEvent $event): void
    {
        try {
            $this->caseStatusLogService->log(
                $event->case->cs_id,
                CasesStatus::STATUS_ERROR,
                $event->oldStatus,
                $event->ownerId,
                $event->creatorId,
                $event->description
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:CasesErrorStatusEventLogListener');
        }
    }
}
