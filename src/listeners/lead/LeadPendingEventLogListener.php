<?php

namespace src\listeners\lead;

use Yii;
use src\events\lead\LeadPendingEvent;
use common\models\Lead;
use src\services\lead\LeadFlowLogService;

/**
 * Class LeadPendingEventLogListener
 *
 * @property LeadFlowLogService $leadFlowLogService
 */
class LeadPendingEventLogListener
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
     * @param LeadPendingEvent $event
     */
    public function handle(LeadPendingEvent $event): void
    {
        try {
            $this->leadFlowLogService->log(
                $event->lead->id,
                Lead::STATUS_PENDING,
                $event->oldStatus,
                $event->newOwnerId,
                $event->creatorId,
                $event->reason,
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadPendingEventLogListener');
        }
    }
}
