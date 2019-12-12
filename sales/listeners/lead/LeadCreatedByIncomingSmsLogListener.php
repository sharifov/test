<?php

namespace sales\listeners\lead;

use Yii;
use sales\events\lead\LeadCreatedByIncomingSmsEvent;
use sales\services\lead\LeadFlowLogService;

class LeadCreatedByIncomingSmsLogListener
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
     * @param LeadCreatedByIncomingSmsEvent $event
     */
    public function handle(LeadCreatedByIncomingSmsEvent $event): void
    {
        $lead = $event->lead;
        try {
            $this->leadFlowLogService->log(
                $lead->id,
                $lead->status,
                null,
                $lead->employee_id,
                null,
                'Created by incoming sms',
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadCreatedByIncomingSmsLogListener');
        }
    }
}