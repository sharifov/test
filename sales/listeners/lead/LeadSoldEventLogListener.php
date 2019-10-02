<?php

namespace sales\listeners\lead;

use sales\events\lead\LeadSoldEvent;
use sales\helpers\user\UserFinder;
use Yii;
use common\models\Lead;
use sales\services\lead\LeadFlowLogService;

/**
 * Class LeadSoldEventLogListener
 *
 * @property LeadFlowLogService $leadFlowLogService
 */
class LeadSoldEventLogListener
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
     * @param LeadSoldEvent $event
     */
    public function handle(LeadSoldEvent $event): void
    {
        try {
            $this->leadFlowLogService->log(
                $event->lead->id,
                Lead::STATUS_SOLD,
                $event->oldStatus,
                $event->ownerId,
                UserFinder::getCurrentUserId(),
                $event->description,
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadSoldEventLogListener');
        }
    }

}
