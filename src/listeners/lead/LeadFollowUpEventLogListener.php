<?php

namespace src\listeners\lead;

use src\helpers\user\UserFinder;
use Yii;
use src\events\lead\LeadFollowUpEvent;
use common\models\Lead;
use src\services\lead\LeadFlowLogService;

/**
 * Class LeadFollowUpEventLogListener
 *
 * @property LeadFlowLogService $leadFlowLogService
 */
class LeadFollowUpEventLogListener
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
     * @param LeadFollowUpEvent $event
     */
    public function handle(LeadFollowUpEvent $event): void
    {
        try {
            $this->leadFlowLogService->log(
                $event->lead->id,
                Lead::STATUS_FOLLOW_UP,
                $event->oldStatus,
                $event->newOwnerId,
                $event->creatorId,
                $event->reason,
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadFollowUpEventLogListener');
        }
    }
}
