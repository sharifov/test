<?php

namespace src\listeners\cases;

use src\entities\cases\CasesStatus;
use src\entities\cases\events\CasesSolvedStatusEvent;
use src\services\cases\CaseStatusLogService;
use Yii;

/**
 * Class CasesSolvedStatusEventLogListener
 *
 * @property CaseStatusLogService $caseStatusLogService
 */
class CasesSolvedStatusEventLogListener
{
    private $caseStatusLogService;

    public function __construct(CaseStatusLogService $caseStatusLogService)
    {
        $this->caseStatusLogService = $caseStatusLogService;
    }

    public function handle(CasesSolvedStatusEvent $event): void
    {
        try {
            $this->caseStatusLogService->log(
                $event->case->cs_id,
                CasesStatus::STATUS_SOLVED,
                $event->oldStatus,
                $event->ownerId,
                $event->creatorId,
                $event->description
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:CasesSolvedStatusEventLogListener');
        }
    }
}
