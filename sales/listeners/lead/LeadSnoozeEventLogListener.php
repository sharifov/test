<?php

namespace sales\listeners\lead;

use sales\events\lead\LeadSnoozeEvent;
use sales\helpers\user\UserFinder;
use Yii;
use common\models\Lead;
use sales\services\lead\LeadFlowLogService;

/**
 * Class LeadSnoozeEventLogListener
 *
 * @property LeadFlowLogService $leadFlowLogService
 */
class LeadSnoozeEventLogListener
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
     * @param LeadSnoozeEvent $event
     */
    public function handle(LeadSnoozeEvent $event): void
    {
        try {
            $this->leadFlowLogService->log(
                $event->lead->id,
                Lead::STATUS_SNOOZE,
                $event->oldStatus,
                $event->ownerId,
                UserFinder::getCurrentUserId(),
                $event->description,
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadSnoozeEventLogListener');
        }
    }

}
