<?php

namespace sales\listeners\cases;

use sales\entities\cases\CasesStatus;
use sales\entities\cases\events\CasesProcessingStatusEvent;
use sales\services\cases\CasesStatusLogService;
use Yii;

/**
 * Class CasesProcessingStatusEventLogListener
 *
 * @property CasesStatusLogService $casesStatusLogService
 */
class CasesProcessingStatusEventLogListener
{
    private $casesStatusLogService;

    /**
     * @param CasesStatusLogService $casesStatusLogService
     */
    public function __construct(CasesStatusLogService $casesStatusLogService)
    {
        $this->casesStatusLogService = $casesStatusLogService;
    }

    /**
     * @param CasesProcessingStatusEvent $event
     */
    public function handle(CasesProcessingStatusEvent $event): void
    {
        try {
            $this->casesStatusLogService->log(
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
