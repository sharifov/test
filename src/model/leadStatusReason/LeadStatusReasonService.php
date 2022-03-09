<?php

namespace src\model\leadStatusReason;

use common\models\Lead;
use common\models\query\LeadFlowQuery;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use modules\qaTask\src\abac\dto\QaTaskAbacDto;
use modules\qaTask\src\entities\qaTaskRules\QaTaskRules;
use modules\qaTask\src\useCases\qaTask\create\lead\closeCheck\QaTaskCreateLeadCloseCheckService;
use modules\qaTask\src\useCases\qaTask\create\lead\closeCheck\Rule;
use src\model\leadUserConversion\entity\LeadUserConversion;
use src\model\leadUserConversion\entity\LeadUserConversionQuery;
use src\model\leadUserConversion\repository\LeadUserConversionRepository;
use src\repositories\lead\LeadRepository;
use src\services\lead\LeadStateService;

/**
 * Class LeadStatusReasonService
 * @package src\model\leadStatusReason
 *
 * @property-read LeadRepository $leadRepository
 * @property-read LeadUserConversionRepository $leadUserConversionRepository
 * @property-read QaTaskCreateLeadCloseCheckService $qaTaskCreateLeadCloseCheckService
 */
class LeadStatusReasonService
{
    private LeadRepository $leadRepository;
    private LeadUserConversionRepository $leadUserConversionRepository;
    private QaTaskCreateLeadCloseCheckService $qaTaskCreateLeadCloseCheckService;

    public function __construct(LeadRepository $leadRepository, LeadUserConversionRepository $leadUserConversionRepository, QaTaskCreateLeadCloseCheckService $qaTaskCreateLeadCloseCheckService)
    {
        $this->leadRepository = $leadRepository;
        $this->leadUserConversionRepository = $leadUserConversionRepository;
        $this->qaTaskCreateLeadCloseCheckService = $qaTaskCreateLeadCloseCheckService;
    }

    public function handleReason(HandleReasonDto $dto): void
    {
        $method = $this->formatKeyToMethodName($dto->leadStatusReasonKey);
        if (method_exists($this, $method)) {
            $this->$method($dto);
        }
//        throw new \RuntimeException('Reason: ' . $leadStatusReason->lsr_name . ' doesnt have handler');
    }

    private function alternative(HandleReasonDto $dto): void
    {
        $this->leadToConversion($dto)->createQaTaskLead($dto);
    }

    private function bookedWithAnotherAgent(HandleReasonDto $dto): void
    {
        $this->toBonusQueue($dto)
            ->leadToConversion($dto)
            ->createQaTaskLead($dto);
    }

    private function canceledTrip(HandleReasonDto $dto): void
    {
        $this->toBonusQueue($dto)
            ->leadToConversion($dto)
            ->createQaTaskLead($dto);
    }

    private function clientAskedNotToBeContactedAgain(HandleReasonDto $dto): void
    {
        $this->toBonusQueue($dto)
            ->leadToConversion($dto)
            ->createQaTaskLead($dto);
    }

    private function clientNeedsNoSales(HandleReasonDto $dto): void
    {
        $this->leadToConversion($dto)->createQaTaskLead($dto);
    }

    private function competitorHasABetterContract(HandleReasonDto $dto): void
    {
        $this->toBonusQueue($dto)
            ->leadToConversion($dto)
            ->createQaTaskLead($dto);
    }

    private function invalid(HandleReasonDto $dto): void
    {
        $this->toBonusQueue($dto)
            ->leadToConversion($dto)
            ->createQaTaskLead($dto);
    }

    private function properFollowUpDone(HandleReasonDto $dto): void
    {
        $this->toBonusQueue($dto)
            ->leadToConversion($dto)
            ->createQaTaskLead($dto);
    }

    private function purchasedElsewhere(HandleReasonDto $dto): void
    {
        $this->toBonusQueue($dto)
            ->leadToConversion($dto)
            ->createQaTaskLead($dto);
    }

    private function travelDatesPassed(HandleReasonDto $dto): void
    {
        $this->toBonusQueue($dto)
            ->leadToConversion($dto)
            ->createQaTaskLead($dto);
    }

    private function transfer(HandleReasonDto $dto): void
    {
        $this->leadToConversion($dto)->createQaTaskLead($dto);
    }

    private function test(HandleReasonDto $dto): void
    {
        $this->leadToConversion($dto)->createQaTaskLead($dto);
    }

    private function duplicated(HandleReasonDto $dto): void
    {
        $stateService = \Yii::createObject(LeadStateService::class);
        $stateService->duplicate($dto->lead, $dto->newLeadOwnerId, ((int)$dto->reason) ?: null, $dto->creatorId);
        $this->leadToConversion($dto)->createQaTaskLead($dto);
    }

    private function toBonusQueue(HandleReasonDto $dto): self
    {
        $dto->lead->followUp($dto->newLeadOwnerId, $dto->creatorId, $dto->reason);
        $this->leadRepository->save($dto->lead);
        return $this;
    }

    private function leadToConversion(HandleReasonDto $dto): self
    {
        $leadClosedCount = LeadFlowQuery::countByStatus($dto->lead->id, Lead::STATUS_CLOSED);
        if ($leadClosedCount <= 1) {
            $abacDto = new LeadAbacDto($dto->lead, $dto->creatorId);
            $abacDto->closeReason = $dto->leadStatusReasonKey;
            if (\Yii::$app->abac->can($abacDto, LeadAbacObject::ACT_USER_CONVERSION, LeadAbacObject::ACTION_CREATE)) {
                $leadUserConversion = LeadUserConversion::create(
                    $dto->lead->id,
                    $dto->lead->employee_id,
                    $dto->reason,
                    $dto->creatorId
                );
                $this->leadUserConversionRepository->save($leadUserConversion);
            } else {
                LeadUserConversionQuery::removeByLeadId($dto->lead->id);
            }
        }
        return $this;
    }

    private function createQaTaskLead(HandleReasonDto $dto): self
    {
        $abacDto = new QaTaskAbacDto(null);
        $abacDto->closeReason = $dto->leadStatusReasonKey;
        if (\Yii::$app->abac->can($abacDto, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_TO_QA_LIST)) {
            if (($parameters = QaTaskRules::getRule(QaTaskCreateLeadCloseCheckService::CATEGORY_KEY)) && $parameters->isEnabled()) {
                $rule = new Rule($parameters->getValue());
                if ($rule->guard($dto->lead->lDep->dep_key ?? null, $dto->lead->project->project_key ?? null)) {
                    $this->qaTaskCreateLeadCloseCheckService->handle($rule, $dto->lead);
                }
            }
        }
        return $this;
    }

    private function formatKeyToMethodName(string $key): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
    }
}
