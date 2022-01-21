<?php

namespace src\listeners\lead;

use src\events\lead\LeadSoldEvent;
use Yii;
use common\models\Lead;
use src\services\lead\LeadFlowLogService;

/**
 * Class LeadSoldEventLogListener
 *
 * @property LeadFlowLogService $leadFlowLogService
 */
class LeadSoldEventLogListener
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
     * @param LeadSoldEvent $event
     */
    public function handle(LeadSoldEvent $event): void
    {
        try {
            $this->leadFlowLogService->log(
                $event->lead->id,
                Lead::STATUS_SOLD,
                $event->oldStatus,
                $event->newOwnerId,
                $event->creatorId,
                $event->reason,
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadSoldEventLogListener');
        }
    }
}
