<?php

namespace sales\listeners\cases;

use sales\entities\cases\CasesStatus;
use sales\entities\cases\events\CasesPendingStatusEvent;
use sales\services\cases\CasesStatusLogService;
use Yii;

/**
 * Class CasesPendingStatusEventLogListener
 */
class CasesPendingStatusEventLogListener
{
    private $casesStatusLogService;

    /**
     * CasesPendingStatusEventLogListener constructor.
     * @param CasesStatusLogService $casesStatusLogService
     */
    public function __construct(CasesStatusLogService $casesStatusLogService)
    {
        $this->casesStatusLogService = $casesStatusLogService;
    }

    /**
     * @param CasesPendingStatusEvent $event
     */
    public function handle(CasesPendingStatusEvent $event): void
    {
        $createdUserId = Yii::$app->user->id ?? null;
        try {
            $this->casesStatusLogService->log(
                $event->case->cs_id,
                CasesStatus::STATUS_PENDING,
                $event->oldStatus,
                $event->ownerId,
                $createdUserId,
                $event->description
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:CasesPendingStatusEventLogListener');
        }
    }
}
