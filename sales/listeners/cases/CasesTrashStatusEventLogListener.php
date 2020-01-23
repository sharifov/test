<?php

namespace sales\listeners\cases;

use sales\entities\cases\CasesStatus;
use sales\entities\cases\events\CasesTrashStatusEvent;
use sales\services\cases\CasesStatusLogService;
use Yii;

/**
 * Class CasesTrashStatusEventLogListener
 */
class CasesTrashStatusEventLogListener
{
    private $casesStatusLogService;

    /**
     * CasesTrashStatusEventLogListener constructor.
     * @param CasesStatusLogService $casesStatusLogService
     */
    public function __construct(CasesStatusLogService $casesStatusLogService)
    {
        $this->casesStatusLogService = $casesStatusLogService;
    }

    /**
     * @param CasesTrashStatusEvent $event
     */
    public function handle(CasesTrashStatusEvent $event): void
    {
        try {
            $this->casesStatusLogService->log(
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
