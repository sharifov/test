<?php

namespace src\model\leadStatusReason;

use common\models\Lead;
use src\model\leadStatusReason\entity\LeadStatusReason;
use src\model\leadStatusReasonLog\entity\LeadStatusReasonLog;
use src\repositories\lead\LeadRepository;
use src\services\lead\LeadStateService;

/**
 * Class LeadStatusReasonService
 * @package src\model\leadStatusReason
 *
 * @property-read LeadRepository $leadRepository
 */
class LeadStatusReasonService
{
    private LeadRepository $leadRepository;

    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    public function handleReason(HandleReasonDto $dto): void
    {
        $method = $this->formatKeyToMethodName($dto->leadStatusReasonKey);
        if (method_exists($this, $method)) {
            $this->$method($dto);
        }
//        throw new \RuntimeException('Reason: ' . $leadStatusReason->lsr_name . ' doesnt have handler');
    }

    private function bookedWithAnotherAgent(HandleReasonDto $dto)
    {
        $this->toBonusQueue($dto);
    }

    private function canceledTrip(HandleReasonDto $dto)
    {
        $this->toBonusQueue($dto);
    }

    private function clientAskedNotToBeContactedAgain(HandleReasonDto $dto)
    {
        $this->toBonusQueue($dto);
    }

    private function competitorHasABetterContract(HandleReasonDto $dto)
    {
        $this->toBonusQueue($dto);
    }

    private function invalid(HandleReasonDto $dto)
    {
        $this->toBonusQueue($dto);
    }

    private function properFollowUpDone(HandleReasonDto $dto)
    {
        $this->toBonusQueue($dto);
    }

    private function purchasedElsewhere(HandleReasonDto $dto)
    {
        $this->toBonusQueue($dto);
    }

    private function travelDatesPassed(HandleReasonDto $dto)
    {
        $this->toBonusQueue($dto);
    }

    private function duplicated(HandleReasonDto $dto)
    {
        $stateService = \Yii::createObject(LeadStateService::class);
        $stateService->duplicate($dto->lead, $dto->newLeadOwnerId, ((int)$dto->reason) ?: null, $dto->creatorId);
    }

    private function toBonusQueue(HandleReasonDto $dto)
    {
        $dto->lead->followUp($dto->newLeadOwnerId, $dto->creatorId, $dto->reason);
        $this->leadRepository->save($dto->lead);
    }

    private function formatKeyToMethodName(string $key): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
    }
}
