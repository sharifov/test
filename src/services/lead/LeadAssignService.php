<?php

namespace src\services\lead;

use common\components\jobs\LeadPoorProcessingJob;
use common\models\Employee;
use common\models\Lead;
use common\models\LeadQcall;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use src\access\EmployeeAccess;
use src\guards\lead\TakeGuard;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\repositories\lead\LeadRepository;
use src\repositories\user\UserRepository;
use src\services\ServiceFinder;
use src\services\TransactionManager;
use Yii;

/**
 * Class LeadAssignService
 *
 * @property LeadRepository $leadRepository
 * @property UserRepository $userRepository
 * @property ServiceFinder $serviceFinder
 * @property TransactionManager $transactionManager
 * @property TakeGuard $takeGuard
 */
class LeadAssignService
{
    private $leadRepository;
    private $userRepository;
    private $serviceFinder;
    private $transactionManager;
    private $takeGuard;

    public function __construct(
        LeadRepository $leadRepository,
        UserRepository $userRepository,
        ServiceFinder $serviceFinder,
        TransactionManager $transactionManager,
        TakeGuard $takeGuard
    ) {
        $this->leadRepository = $leadRepository;
        $this->userRepository = $userRepository;
        $this->serviceFinder = $serviceFinder;
        $this->transactionManager = $transactionManager;
        $this->takeGuard = $takeGuard;
    }

    /**
     * @param $lead
     * @param $user
     * @param int|null $creatorId
     * @param string|null $reason
     * @throws \Throwable
     */
    public function take($lead, $user, ?int $creatorId = null, ?string $reason = ''): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $user = $this->serviceFinder->userFind($user);
        $oldStatus = $lead->status;

        EmployeeAccess::leadAccess($lead, $user);

        if ($lead->isCompleted()) {
            throw new \DomainException('Lead is completed!');
        }

        if (!$lead->isAvailableToTake()) {
            throw new \DomainException('Lead is unavailable to "Take" now!');
        }

        $leadAbacDto = new LeadAbacDto($lead, $user->getId());
        /** @abac $leadAbacDto, LeadAbacObject::ACT_TAKE_LEAD, LeadAbacObject::ACTION_ACCESS, Access to take lead */
        if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::ACT_TAKE_LEAD, LeadAbacObject::ACTION_ACCESS)) {
            throw new \DomainException('Access is denied');
        }

        $lead->processing($user->id, $creatorId, $reason);

        $this->transactionManager->wrap(function () use ($lead) {
            if ($qCall = LeadQcall::find()->andWhere(['lqc_lead_id' => $lead->id])->one()) {
                $qCall->delete();
            }
            $this->leadRepository->save($lead);
        });

        if (
            $oldStatus === Lead::STATUS_EXTRA_QUEUE &&
            LeadPoorProcessingDataQuery::isExistActiveRule(LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_TAKE)
        ) {
            LeadPoorProcessingService::addLeadPoorProcessingJob(
                $lead->id,
                [LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_TAKE],
                LeadPoorProcessingLogStatus::REASON_TAKE
            );
        }
    }

    /**
     * @param $lead
     * @param $user
     * @param int|null $creatorId
     * @param string|null $reason
     * @throws \Throwable
     */
    public function takeOver($lead, $user, ?int $creatorId = null, ?string $reason = ''): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $user = $this->serviceFinder->userFind($user);
        $oldStatus = $lead->status;

        EmployeeAccess::leadAccess($lead, $user);

        if ($lead->isCompleted()) {
            throw new \DomainException('Lead is completed!');
        }

        if (!$lead->isAvailableToTakeOver()) {
            throw new \DomainException('Lead is unavailable to "Take Over" now!');
        }

        $lead->processing($user->id, $creatorId, $reason);

        $this->transactionManager->wrap(function () use ($lead) {
            if ($qCall = LeadQcall::find()->andWhere(['lqc_lead_id' => $lead->id])->one()) {
                $qCall->delete();
            }
            $this->leadRepository->save($lead);
        });

        if (
            $oldStatus === Lead::STATUS_EXTRA_QUEUE &&
            LeadPoorProcessingDataQuery::isExistActiveRule(LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_TAKE)
        ) {
            LeadPoorProcessingService::addLeadPoorProcessingJob(
                $lead->id,
                [LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_TAKE],
                LeadPoorProcessingLogStatus::REASON_TAKE
            );
        }
    }

    private function checkTakeAccess(Lead $lead, Employee $user): void
    {
        if ($user->isAgent() && ($lead->isPending() || $lead->isBookFailed())) {
            if ($lead->isPending()) {
                $this->takeGuard->minPercentGuard($user);
            }
            $fromStatuses = [];
            if ($lead->isBookFailed()) {
                $fromStatuses = [Lead::STATUS_BOOK_FAILED];
            }
            $this->takeGuard->frequencyMinutesGuard($user, [], $fromStatuses);
        }
    }
}
