<?php

namespace sales\listeners\lead;

use sales\events\lead\LeadCreatedClientChatEvent;
use sales\events\lead\LeadCreatedNewEvent;
use Yii;
use common\models\Lead;
use sales\services\lead\LeadFlowLogService;

/**
 * Class LeadCreatedClientChatLogListener
 *
 * @property LeadFlowLogService $leadFlowLogService
 */
class LeadCreatedClientChatLogListener
{
    private $leadFlowLogService;

    /**
     * @param LeadFlowLogService $leadFlowLogService
     */
    public function __construct(LeadFlowLogService $leadFlowLogService)
    {
        $this->leadFlowLogService = $leadFlowLogService;
    }

    public function handle(LeadCreatedClientChatEvent $event): void
    {
        try {
            $this->leadFlowLogService->log(
                $event->lead->id,
                $event->lead->status,
                null,
                null,
                $event->creatorId,
                null,
                $event->created
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadCreatedClientChatLogListener');
        }
    }
}
