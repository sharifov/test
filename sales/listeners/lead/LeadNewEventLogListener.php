<?php

namespace sales\listeners\lead;

use sales\events\lead\LeadNewEvent;
use Yii;
use common\models\Lead;
use sales\services\lead\LeadFlowLogService;

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
