<?php

namespace src\listeners\lead;

use Yii;
use src\events\lead\LeadRejectEvent;
use common\models\Lead;
use src\services\lead\LeadFlowLogService;

/**
 * Class LeadRejectEventLogListener
 *
 * @property LeadFlowLogService $leadFlowLogService
 */
class LeadRejectEventLogListener
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
     * @param LeadRejectEvent $event
     */
    public function handle(LeadRejectEvent $event): void
    {
        try {
            $this->leadFlowLogService->log(
                $event->lead->id,
                Lead::STATUS_REJECT,
                $event->oldStatus,
                $event->newOwnerId,
                $event->creatorId,
                $event->reason,
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadRejectEventLogListener');
        }
    }
}
