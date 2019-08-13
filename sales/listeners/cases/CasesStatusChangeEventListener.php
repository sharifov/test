<?php

namespace sales\listeners\cases;

use sales\entities\cases\events\CasesStatusChangeEvent;
use sales\services\cases\CasesStatusLogService;
use Yii;

/**
 * Class CasesStatusChangeEventListener
 *
 * @property CasesStatusLogService $casesStatusLogService
 */
class CasesStatusChangeEventListener
{

    private $casesStatusLogService;

    public function __construct(CasesStatusLogService $casesStatusLogService)
    {
        $this->casesStatusLogService = $casesStatusLogService;
    }

    /**
     * @param CasesStatusChangeEvent $event
     */
    public function handle(CasesStatusChangeEvent $event): void
    {
        $createdUserId = Yii::$app->user->id ?? null;
        try {
            $this->casesStatusLogService->log($event->case->cs_id, $event->toStatus, $event->fromStatus, $event->ownerId, $createdUserId);
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:CasesStatusChangeEventListener');
        }
    }

}