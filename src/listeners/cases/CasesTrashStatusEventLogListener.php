<?php

namespace src\listeners\cases;

use src\entities\cases\CasesStatus;
use src\entities\cases\events\CasesTrashStatusEvent;
use src\services\cases\CaseStatusLogService;
use Yii;

/**
 * Class CasesTrashStatusEventLogListener
 *
 * @property CaseStatusLogService $caseStatusLogService
 */
class CasesTrashStatusEventLogListener
{
    private $caseStatusLogService;

    public function __construct(CaseStatusLogService $caseStatusLogService)
    {
        $this->caseStatusLogService = $caseStatusLogService;
    }

    public function handle(CasesTrashStatusEvent $event): void
    {
        try {
            $this->caseStatusLogService->log(
                $event->case->cs_id,
                CasesStatus::STATUS_TRASH,
                $event->oldStatus,
                $event->ownerId,
                $event->creatorId,
                $event->description
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:CasesTrashStatusEventLogListener');
        }
    }
}
