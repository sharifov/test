<?php

namespace sales\listeners\lead;

use Yii;
use sales\events\lead\LeadCreatedByApiEvent;
use sales\services\lead\LeadFlowLogService;

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
                $lead->status,
                null,
                $lead->employee_id,
                null,
                'Created by API',
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadCreatedByApiLogEventListener');
        }
    }
}
