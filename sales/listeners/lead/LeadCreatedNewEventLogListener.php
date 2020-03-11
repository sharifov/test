<?php

namespace sales\listeners\lead;

use sales\events\lead\LeadCreatedNewEvent;
use Yii;
use common\models\Lead;
use sales\services\lead\LeadFlowLogService;

/**
 * Class LeadCreatedNewEventLogListener
 *
 * @property LeadFlowLogService $leadFlowLogService
 */
class LeadCreatedNewEventLogListener
{
    private $leadFlowLogService;

    public function __construct(LeadFlowLogService $leadFlowLogService)
    {
        $this->leadFlowLogService = $leadFlowLogService;
    }

    public function handle(LeadCreatedNewEvent $event): void
    {
        try {
            $this->leadFlowLogService->log(
                $event->lead->id,
                Lead::STATUS_NEW,
                null,
                null,
                $event->creatorId,
                null,
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadCreatedNewEventLogListener');
        }
    }
}
