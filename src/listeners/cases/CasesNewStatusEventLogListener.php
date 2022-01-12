<?php

namespace src\listeners\cases;

use src\entities\cases\CasesStatus;
use src\entities\cases\events\CasesNewStatusEvent;
use src\services\cases\CaseStatusLogService;
use Yii;

/**
 * Class CasesNewStatusEventLogListener
 *
 * @property CaseStatusLogService $caseStatusLogService
 */
class CasesNewStatusEventLogListener
{
    private $caseStatusLogService;

    public function __construct(CaseStatusLogService $caseStatusLogService)
    {
        $this->caseStatusLogService = $caseStatusLogService;
    }

    public function handle(CasesNewStatusEvent $event): void
    {
        try {
            $this->caseStatusLogService->log(
                $event->case->cs_id,
                CasesStatus::STATUS_NEW,
                $event->oldStatus,
                $event->ownerId,
                $event->creatorId,
                $event->description
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:CasesNewStatusEventLogListener');
        }
    }
}
