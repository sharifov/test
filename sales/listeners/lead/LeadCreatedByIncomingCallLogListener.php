<?php

namespace sales\listeners\lead;

use Yii;
use sales\events\lead\LeadCreatedByIncomingCallEvent;
use sales\services\lead\LeadFlowLogService;

/**
 * Class LeadCreatedByIncomingCallListener
 *
 * @property LeadFlowLogService $leadFlowLogService
 */
class LeadCreatedByIncomingCallLogListener
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
     * @param LeadCreatedByIncomingCallEvent $event
     */
    public function handle(LeadCreatedByIncomingCallEvent $event): void
    {
        $lead = $event->lead;
        try {
            $this->leadFlowLogService->log(
                $lead->id,
                $lead->status,
                null,
                $lead->employee_id,
                null,
                'Created by incoming call',
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadCreatedByIncomingCallLogListener');
        }
    }
}
