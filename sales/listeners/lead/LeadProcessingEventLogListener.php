<?php

namespace sales\listeners\lead;

use Yii;
use common\models\Lead;
use sales\events\lead\LeadProcessingEvent;
use sales\services\lead\LeadFlowLogService;

/**
 * Class LeadProcessingEventLogListener
 *
 * @property LeadFlowLogService $leadFlowLogService
 */
class LeadProcessingEventLogListener
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
     * @param LeadProcessingEvent $event
     */
    public function handle(LeadProcessingEvent $event): void
    {
        try {
            $this->leadFlowLogService->log(
                $event->lead->id,
                Lead::STATUS_PROCESSING,
                $event->oldStatus,
                $event->newOwnerId,
                $event->creatorId,
                $event->reason,
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadProcessingEventLogListener');
        }
    }

}
