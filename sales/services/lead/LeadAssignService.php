<?php

namespace sales\services\lead;

use common\models\Employee;
use common\models\Lead;
use sales\access\EmployeeAccess;
use sales\repositories\lead\LeadRepository;
use sales\repositories\user\UserRepository;
use yii\web\ForbiddenHttpException;

/**
 * Class LeadAssignService
 *
 * @property LeadRepository $leadRepository
 * @property UserRepository $userRepository
 */
class LeadAssignService
{

    private $leadRepository;
    private $userRepository;

    public function __construct(LeadRepository $leadRepository, UserRepository $userRepository)
    {
        $this->leadRepository = $leadRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $leadId
     * @param int $userId
     * @param string|null $description
     */
    public function processing(int $leadId, int $userId, ?string $description = ''): void
    {
        $lead = $this->leadRepository->find($leadId);
        $user = $this->userRepository->find($userId);

        EmployeeAccess::leadAccess($lead, $user);

        if ($lead->isCompleted()) {
            throw new \DomainException('Lead is completed!');
        }

        if (!$lead->isAvailableToProcessing()) {
            throw new \DomainException('Lead is unavailable to "Processing" now!');
        }

        $lead->processing($user->id, $description);
        $this->leadRepository->save($lead);
    }

    /**
     * @param int $leadId
     * @param int $userId
     * @param string|null $description
     * @throws ForbiddenHttpException
     */
    public function take(int $leadId, int $userId, ?string $description = ''): void
    {
        $lead = $this->leadRepository->find($leadId);
        $user = $this->userRepository->find($userId);

        EmployeeAccess::leadAccess($lead, $user);
        self::checkAccess($lead, $user);

        if ($lead->isCompleted()) {
            throw new \DomainException('Lead is completed!');
        }

        if (!$lead->isAvailableToTake()) {
            throw new \DomainException('Lead is unavailable to "Take" now!');
        }

        $lead->processing($user->id, $description);

        $this->leadRepository->save($lead);
    }

    /**
     * @param int $leadId
     * @param int $userId
     * @param string|null $description
     * @throws ForbiddenHttpException
     */
    public function takeOver(int $leadId, int $userId, ?string $description = ''): void
    {
        $lead = $this->leadRepository->find($leadId);
        $user = $this->userRepository->find($userId);

        EmployeeAccess::leadAccess($lead, $user);
        self::checkAccess($lead, $user);

        if ($lead->isCompleted()) {
            throw new \DomainException('Lead is completed!');
        }

        if (!$lead->isAvailableToTakeOver()) {
            throw new \DomainException('Lead is unavailable to "Take Over" now!');
        }

        $lead->processing($user->id, $description);

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
