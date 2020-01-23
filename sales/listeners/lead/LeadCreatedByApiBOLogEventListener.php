<?php

namespace sales\listeners\lead;

use common\models\LeadFlow;
use sales\events\lead\LeadCreatedByApiBOEvent;
use Yii;
use sales\services\lead\LeadFlowLogService;

/**
 * Class LeadCreatedByApiBOLogEventListener
 *
 * @property LeadFlowLogService $leadFlowLogService
 */
class LeadCreatedByApiBOLogEventListener
{
    private $leadFlowLogService;

    public function __construct(LeadFlowLogService $leadFlowLogService)
    {
        $this->leadFlowLogService = $leadFlowLogService;
    }

    public function handle(LeadCreatedByApiBOEvent $event): void
    {
        $lead = $event->lead;
        try {
            $this->leadFlowLogService->log(
                $lead->id,
                $event->status,
                null,
                $lead->employee_id,
                null,
                LeadFlow::REASON_CREATED_BY_API,
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadCreatedByApiBOLogEventListener');
        }
    }
}
