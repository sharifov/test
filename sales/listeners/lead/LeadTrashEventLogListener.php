<?php

namespace sales\listeners\lead;

use sales\events\lead\LeadTrashEvent;
use sales\helpers\user\UserFinder;
use Yii;
use common\models\Lead;
use sales\services\lead\LeadFlowLogService;

/**
 * Class LeadTrashEventLogListener
 *
 * @property LeadFlowLogService $leadFlowLogService
 */
class LeadTrashEventLogListener
{

    private $leadFlowLogService;

    /**
     * @param LeadFlowLogService $leadFlowLogService
     */
    public function __construct(LeadFlowLogService $leadFlowLogService)
    {
        $this->leadFlowLogService = $leadFlowLogService;
    }

    /**
     * @param LeadTrashEvent $event
     */
    public function handle(LeadTrashEvent $event): void
    {
        try {
            $this->leadFlowLogService->log(
                $event->lead->id,
                Lead::STATUS_TRASH,
                $event->oldStatus,
                $event->ownerId,
                UserFinder::getCurrentUserId(),
                $event->description,
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadTrashEventLogListener');
        }
    }

}
