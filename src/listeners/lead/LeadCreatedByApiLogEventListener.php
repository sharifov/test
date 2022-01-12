<?php

namespace src\listeners\lead;

use common\models\LeadFlow;
use Yii;
use src\events\lead\LeadCreatedByApiEvent;
use src\services\lead\LeadFlowLogService;

/**
 * Class LeadCreatedByApiLogEventListener
 *
 * @property LeadFlowLogService $leadFlowLogService
 */
class LeadCreatedByApiLogEventListener
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
     * @param LeadCreatedByApiEvent $event
     */
    public function handle(LeadCreatedByApiEvent $event): void
    {
        $lead = $event->lead;
        try {
            $this->leadFlowLogService->log(
                $lead->id,
                $event->newStatus,
                null,
                $lead->employee_id,
                null,
                LeadFlow::REASON_CREATED_BY_API,
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadCreatedByApiLogEventListener');
        }
    }
}
