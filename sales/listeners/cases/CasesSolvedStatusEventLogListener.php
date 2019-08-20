<?php

namespace sales\listeners\cases;

use sales\entities\cases\CasesStatus;
use sales\entities\cases\events\CasesSolvedStatusEvent;
use sales\services\cases\CasesStatusLogService;
use Yii;

/**
 * Class CasesSolvedStatusEventLogListener
 */
class CasesSolvedStatusEventLogListener
{
    private $casesStatusLogService;

    /**
     * CasesSolvedStatusEventLogListener constructor.
     * @param CasesStatusLogService $casesStatusLogService
     */
    public function __construct(CasesStatusLogService $casesStatusLogService)
    {
        $this->casesStatusLogService = $casesStatusLogService;
    }

    /**
     * @param CasesSolvedStatusEvent $event
     */
    public function handle(CasesSolvedStatusEvent $event): void
    {
        $createdUserId = Yii::$app->user->id ?? null;
        try {
            $this->casesStatusLogService->log(
                $event->case->cs_id,
                CasesStatus::STATUS_SOLVED,
                $event->oldStatus,
                $event->ownerId,
                $createdUserId,
                $event->description
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:CasesSolvedStatusEventLogListener');
        }
    }
}