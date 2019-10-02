<?php

namespace sales\listeners\lead;

use sales\helpers\user\UserFinder;
use Yii;
use sales\events\lead\LeadOnHoldEvent;
use common\models\Lead;
use sales\services\lead\LeadFlowLogService;

/**
 * Class LeadOnHoldEventLogListener
 *
 * @property LeadFlowLogService $leadFlowLogService
 */
class LeadOnHoldEventLogListener
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
     * @param LeadOnHoldEvent $event
     */
    public function handle(LeadOnHoldEvent $event): void
    {
        try {
            $this->leadFlowLogService->log(
                $event->lead->id,
                Lead::STATUS_ON_HOLD,
                $event->oldStatus,
                $event->ownerId,
                UserFinder::getCurrentUserId(),
                $event->description,
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadOnHoldEventLogListener');
        }
    }
}
