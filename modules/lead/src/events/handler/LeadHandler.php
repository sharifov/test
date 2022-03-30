<?php

namespace modules\lead\src\events\handler;

use src\model\leadStatusReason\HandleReasonDto;
use src\model\leadStatusReason\LeadStatusReasonService;
use yii\helpers\VarDumper;

class LeadHandler
{
    /**
     * @var LeadStatusReasonService
     */
    private LeadStatusReasonService $leadStatusReasonService;

    public function __construct(LeadStatusReasonService $leadStatusReasonService)
    {
        $this->leadStatusReasonService = $leadStatusReasonService;
    }

    public const CLOSE = 'close';

    public function close(?array $eventData = [], ?array $eventParams = [], ?array $handlerParams = []): void
    {
        $dto = $eventData['dto'] ?? null;
        if (!$dto || !($dto instanceof HandleReasonDto)) {
            throw new \RuntimeException('Event does not have dto parameter');
        }
        $this->leadStatusReasonService->handleReason($dto);
    }
}
