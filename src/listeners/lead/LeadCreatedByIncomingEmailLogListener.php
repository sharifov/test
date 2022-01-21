<?php

namespace src\listeners\lead;

use Yii;
use src\events\lead\LeadCreatedByIncomingEmailEvent;
use src\services\lead\LeadFlowLogService;

class LeadCreatedByIncomingEmailLogListener
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
     * @param LeadCreatedByIncomingEmailEvent $event
     */
    public function handle(LeadCreatedByIncomingEmailEvent $event): void
    {
        $lead = $event->lead;
        try {
            $this->leadFlowLogService->log(
                $lead->id,
                $lead->status,
                null,
                $lead->employee_id,
                null,
                'Created by incoming email',
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadCreatedByIncomingEmailLogListener');
        }
    }
}
