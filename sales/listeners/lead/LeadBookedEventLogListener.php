<?php

namespace sales\listeners\lead;

use sales\events\lead\LeadBookedEvent;
use sales\helpers\user\UserFinder;
use Yii;
use common\models\Lead;
use sales\services\lead\LeadFlowLogService;

/**
 * Class LeadBookedEventLogListener
 *
 * @property LeadFlowLogService $leadFlowLogService
 */
class LeadBookedEventLogListener
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
     * @param LeadBookedEvent $event
     */
    public function handle(LeadBookedEvent $event): void
    {
        try {
            $this->leadFlowLogService->log(
                $event->lead->id,
                Lead::STATUS_BOOKED,
                $event->oldStatus,
                $event->ownerId,
                UserFinder::getCurrentUserId(),
                $event->description,
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadBookedEventLogListener');
        }
    }

}
