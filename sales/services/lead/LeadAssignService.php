<?php

namespace sales\services\lead;

use common\models\Employee;
use common\models\Lead;
use sales\access\EmployeeAccess;
use sales\repositories\lead\LeadRepository;
use sales\repositories\user\UserRepository;
use sales\services\ServiceFinder;
use yii\web\ForbiddenHttpException;

/**
 * Class LeadAssignService
 *
 * @property LeadRepository $leadRepository
 * @property UserRepository $userRepository
 * @property ServiceFinder $serviceFinder
 */
class LeadAssignService
{

    private $leadRepository;
    private $userRepository;
    private $serviceFinder;

    public function __construct(LeadRepository $leadRepository, UserRepository $userRepository, ServiceFinder $serviceFinder)
    {
        $this->leadRepository = $leadRepository;
        $this->userRepository = $userRepository;
        $this->serviceFinder = $serviceFinder;
    }

    /**
     * @param int|Lead $lead
     * @param int|Employee $user
     * @param int|null $creatorId
     * @param string|null $reason
     * @throws ForbiddenHttpException
     */
    public function take($lead, $user, ?int $creatorId = null, ?string $reason = ''): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $user = $this->serviceFinder->userFind($user);

        EmployeeAccess::leadAccess($lead, $user);
        self::checkAccess($lead, $user);

        if ($lead->isCompleted()) {
            throw new \DomainException('Lead is completed!');
        }

        if (!$lead->isAvailableToTake()) {
            throw new \DomainException('Lead is unavailable to "Take" now!');
        }

        $lead->processing($user->id, $creatorId, $reason);

        $this->leadRepository->save($lead);
    }

    /**
     * @param int|Lead $lead
     * @param int|Employee $user
     * @param int|null $creatorId
     * @param string|null $reason
     * @throws ForbiddenHttpException
     */
    public function takeOver($lead, $user, ?int $creatorId = null, ?string $reason = ''): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $user = $this->serviceFinder->userFind($user);

        EmployeeAccess::leadAccess($lead, $user);
        self::checkAccess($lead, $user);

        if ($lead->isCompleted()) {
            throw new \DomainException('Lead is completed!');
        }

        if (!$lead->isAvailableToTakeOver()) {
            throw new \DomainException('Lead is unavailable to "Take Over" now!');
        }

        $lead->processing($user->id, $creatorId, $reason);

        $this->leadRepository->save($lead);
    }

    /**
     * @param Lead $lead
     * @param Employee $user
     * @throws ForbiddenHttpException
     */
    private static function checkAccess(Lead $lead, Employee $user): void
    {
        if ($lead->isPending() && $user->isAgent()) {
            $isAccessNewLead = $user->accessTakeNewLead();
            if (!$isAccessNewLead) {
                throw new ForbiddenHttpException('Access is denied (limit) - "Take lead"');
            }
            $isAccessNewLeadByFrequency = $user->accessTakeLeadByFrequencyMinutes();
            if (!$isAccessNewLeadByFrequency['access']) {
                throw new ForbiddenHttpException('Access is denied (frequency) - "Take lead"');
            }
        }
    }

}
