<?php

namespace sales\listeners\cases;

use sales\entities\cases\CasesStatus;
use sales\entities\cases\events\CasesAutoProcessingStatusEvent;
use sales\services\cases\CaseStatusLogService;
use Yii;

/**
 * Class CasesAutoProcessingStatusEventListener
 *
 * @property CaseStatusLogService $caseStatusLogService
 */
class CasesAutoProcessingStatusEventListener
{
    private $caseStatusLogService;

    public function __construct(CaseStatusLogService $caseStatusLogService)
    {
        $this->caseStatusLogService = $caseStatusLogService;
    }

    public function handle(CasesAutoProcessingStatusEvent $event): void
    {
        try {
            $this->caseStatusLogService->log(
                $event->case->cs_id,
                CasesStatus::STATUS_AUTO_PROCESSING,
                $event->oldStatus,
                $event->ownerId,
                $event->creatorId,
                $event->description
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:CasesAutoProcessingStatusEventLogListener');
        }
    }
}
