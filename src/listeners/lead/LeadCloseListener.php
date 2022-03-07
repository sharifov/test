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
 * @property-read LeadStatusReasonService $leadStatusReasonService
 */
class LeadCloseListener
{
    private LeadFlowLogService $leadFlowLogService;
    private LeadStatusReasonLogRepository $leadStatusReasonLogRepository;
    private LeadStatusReasonService $leadStatusReasonService;

    public function __construct(
        LeadFlowLogService $leadFlowLogService,
        LeadStatusReasonLogRepository $leadStatusReasonLogRepository,
        LeadStatusReasonService $leadStatusReasonService
    ) {
        $this->leadFlowLogService = $leadFlowLogService;
        $this->leadStatusReasonLogRepository = $leadStatusReasonLogRepository;
        $this->leadStatusReasonService = $leadStatusReasonService;
    }

    public function handle(LeadCloseEvent $event)
    {
        $leadFlow = $this->leadFlowLogService->log(
            $event->lead->id,
            Lead::STATUS_CLOSED,
            $event->oldStatus,
            null,
            $event->creatorId,
            null,
            null
        );

        $leadStatusReason = LeadStatusReasonQuery::getLeadStatusReasonByKey($event->leadStatusReasonKey);
        if ($leadStatusReason) {
            $leadStatusReasonLog = LeadStatusReasonLog::create($leadFlow->id, $leadStatusReason->lsr_id, $event->reasonComment);
            $this->leadStatusReasonLogRepository->save($leadStatusReasonLog);
        }

        $dto = new HandleReasonDto(
            $event->lead,
            $event->leadStatusReasonKey,
            null,
            $event->creatorId,
            $event->reasonComment
        );
        $this->leadStatusReasonService->handleReason($dto);
    }
}
