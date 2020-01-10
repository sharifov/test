<?php

namespace sales\listeners\cases;

use sales\entities\cases\CasesStatus;
use sales\entities\cases\events\CasesFollowUpStatusEvent;
use sales\services\cases\CasesStatusLogService;
use Yii;

/**
 * Class CasesFollowUpStatusEventLogListener
 */
class CasesFollowUpStatusEventLogListener
{
    private $casesStatusLogService;

    /**
     * CasesFollowUpStatusEventLogListener constructor.
     * @param CasesStatusLogService $casesStatusLogService
     */
    public function __construct(CasesStatusLogService $casesStatusLogService)
    {
        $this->casesStatusLogService = $casesStatusLogService;
    }

    /**
     * @param CasesFollowUpStatusEvent $event
     */
    public function handle(CasesFollowUpStatusEvent $event): void
    {
        try {
            $this->casesStatusLogService->log(
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
