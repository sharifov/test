<?php

namespace src\listeners\lead;

use common\models\Lead;
use src\events\lead\LeadCloseEvent;
use src\model\leadStatusReason\entity\LeadStatusReasonQuery;
use src\model\leadStatusReason\HandleReasonDto;
use src\model\leadStatusReason\LeadStatusReasonService;
use src\model\leadStatusReasonLog\entity\LeadStatusReasonLog;
use src\model\leadStatusReasonLog\LeadStatusReasonLogRepository;
use src\services\lead\LeadFlowLogService;

/**
 * Class LeadCloseListener
 * @package src\listeners\lead
 *
 * @property-read LeadFlowLogService $leadFlowLogService
 * @property-read LeadStatusReasonLogRepository $leadStatusReasonLogRepository
 */
class LeadCloseListener
{
    private LeadFlowLogService $leadFlowLogService;
    private LeadStatusReasonLogRepository $leadStatusReasonLogRepository;

    public function __construct(
        LeadFlowLogService $leadFlowLogService,
        LeadStatusReasonLogRepository $leadStatusReasonLogRepository
    ) {
        $this->leadFlowLogService = $leadFlowLogService;
        $this->leadStatusReasonLogRepository = $leadStatusReasonLogRepository;
    }

    public function handle(LeadCloseEvent $event)
    {
        $leadStatusReason = LeadStatusReasonQuery::getLeadStatusReasonByKey($event->leadStatusReasonKey);

        $reason = $leadStatusReason->lsr_name ?? '';
        if ($event->reasonComment) {
            $reason .= ': ' . $event->reasonComment;
        }
        $leadFlow = $this->leadFlowLogService->log(
            $event->lead->id,
            Lead::STATUS_CLOSED,
            $event->oldStatus,
            null,
            $event->creatorId,
            $reason,
            null
        );

        if ($leadStatusReason) {
            $leadStatusReasonLog = LeadStatusReasonLog::create($leadFlow->id, $leadStatusReason->lsr_id, $event->reasonComment);
            $this->leadStatusReasonLogRepository->save($leadStatusReasonLog);
        }
    }
}
