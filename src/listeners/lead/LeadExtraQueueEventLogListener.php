<?php

namespace src\listeners\lead;

use src\events\lead\LeadExtraQueueEvent;
use Yii;
use common\models\Lead;
use src\services\lead\LeadFlowLogService;

/**
 * Class LeadExtraQueueEventLogListener
 *
 * @property LeadFlowLogService $leadFlowLogService
 */
class LeadExtraQueueEventLogListener
{
    private LeadFlowLogService $leadFlowLogService;

    /**
     * @param LeadFlowLogService $leadFlowLogService
     */
    public function __construct(LeadFlowLogService $leadFlowLogService)
    {
        $this->leadFlowLogService = $leadFlowLogService;
    }

    public function handle(LeadExtraQueueEvent $event): void
    {
        try {
            $this->leadFlowLogService->log(
                $event->getLead()->id,
                Lead::STATUS_EXTRA_QUEUE,
                $event->oldStatus,
                $event->newOwnerId,
                $event->creatorId,
                $event->reason,
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadExtraQueueEventLogListener');
        }
    }
}
