<?php

namespace src\listeners\lead;

use src\events\lead\LeadNewEvent;
use Yii;
use common\models\Lead;
use src\services\lead\LeadFlowLogService;

/**
 * Class LeadNewEventLogListener
 *
 * @property LeadFlowLogService $leadFlowLogService
 */
class LeadNewEventLogListener
{
    private $leadFlowLogService;

    public function __construct(LeadFlowLogService $leadFlowLogService)
    {
        $this->leadFlowLogService = $leadFlowLogService;
    }

    public function handle(LeadNewEvent $event): void
    {
        try {
            $this->leadFlowLogService->log(
                $event->lead->id,
                Lead::STATUS_NEW,
                $event->oldStatus,
                $event->newOwnerId,
                $event->creatorId,
                $event->reason,
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadNewEventLogListener');
        }
    }
}
