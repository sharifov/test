<?php

namespace sales\services\lead;

use common\models\Employee;
use common\models\Lead;
use sales\repositories\lead\LeadRepository;
use sales\repositories\user\UserRepository;
use yii\web\ForbiddenHttpException;

/**
 * Class LeadAssignService
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
     * @param Lead $lead
     * @param Employee $user
     * @throws ForbiddenHttpException
     */
    private function checkAccess(Lead $lead, Employee $user): void
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

    /**
     * @param int $leadId
     * @param int $userId
     * @throws ForbiddenHttpException
     */
    public function take(int $leadId, int $userId): void
    {
        $lead = $this->leadRepository->find($leadId);
        $user = $this->userRepository->find($userId);

        $this->checkAccess($lead, $user);

        if ($lead->isCompleted()) {
            throw new \DomainException('Lead is completed!');
        }

        if ($lead->isAlreadyTakenUser($userId)) {
            throw new \DomainException('Lead is already taken to this user!');
        }

        if (!$lead->isAvailableToTake()) {
            throw new \DomainException('Lead is unavailable to "Take" now!');
        }

        $lead->processing($user->id);


        $this->leadRepository->save($lead);
    }

    /**
     * @param int $leadId
     * @param int $userId
     * @throws ForbiddenHttpException
     */
    public function takeOver(int $leadId, int $userId): void
    {
        $lead = $this->leadRepository->find($leadId);
        $user = $this->userRepository->find($userId);

        $this->checkAccess($lead, $user);

        $lead->takeOver($user->id);
        $this->leadRepository->save($lead);
    }

}
