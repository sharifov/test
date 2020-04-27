<?php

namespace sales\services\lead;

use common\models\Employee;
use common\models\Lead;
use common\models\LeadQcall;
use sales\access\EmployeeAccess;
use sales\guards\lead\TakeGuard;
use sales\repositories\lead\LeadRepository;
use sales\repositories\user\UserRepository;
use sales\services\ServiceFinder;
use sales\services\TransactionManager;

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
    )
    {
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

        EmployeeAccess::leadAccess($lead, $user);

        if ($lead->isCompleted()) {
            throw new \DomainException('Lead is completed!');
        }

        if (!$lead->isAvailableToTake()) {
            throw new \DomainException('Lead is unavailable to "Take" now!');
        }

        $this->checkTakeAccess($lead, $user);

        $lead->processing($user->id, $creatorId, $reason);

        $this->transactionManager->wrap(function () use ($lead) {
            if ($qCall = LeadQcall::find()->andWhere(['lqc_lead_id' => $lead->id])->one()) {
                $qCall->delete();
            }
            $this->leadRepository->save($lead);
        });
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
