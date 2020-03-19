<?php

namespace sales\listeners\cases;

use sales\entities\cases\events\CasesStatusChangeEvent;
use sales\services\cases\CaseStatusLogService;
use Yii;

/**
 * Class CasesStatusChangeEventListener
 *
 * @property CaseStatusLogService $caseStatusLogService
 */
class CasesStatusChangeEventListener
{
    private $caseStatusLogService;

    public function __construct(CaseStatusLogService $caseStatusLogService)
    {
        $this->caseStatusLogService = $caseStatusLogService;
    }

    /**
     * @param CasesStatusChangeEvent $event
     */
    public function handle(CasesStatusChangeEvent $event): void
    {
        $createdUserId = Yii::$app->user->id ?? null;
        try {
            $this->caseStatusLogService->log(
                $event->case->cs_id,
                $event->toStatus,
                $event->fromStatus,
                $event->ownerId,
                $createdUserId,
                null
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:CasesStatusChangeEventListener');
        }
    }
}
