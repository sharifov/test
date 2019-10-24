<?php

namespace sales\services\lead;

use common\models\Call;
use common\models\Employee;
use common\models\Lead;
use common\models\LeadQcall;
use sales\access\EmployeeAccess;
use sales\repositories\lead\LeadRepository;
use sales\services\ServiceFinder;

/**
 * Class LeadRedialService
 *
 * @property LeadRepository $leadRepository
 * @property ServiceFinder $serviceFinder
 */
class LeadRedialService
{

    private $leadRepository;
    private $serviceFinder;

    public function __construct(LeadRepository $leadRepository,  ServiceFinder $serviceFinder)
    {
        $this->leadRepository = $leadRepository;
        $this->serviceFinder = $serviceFinder;
    }

    /**
     * @param int|Lead $lead
     * @param int|Employee $user
     */
    public function redial($lead, $user): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $user = $this->serviceFinder->userFind($user);

        EmployeeAccess::leadAccess($lead, $user);

        $this->guardUserFree($user->id);
        $this->guardLeadForRedial($lead);

        $lead->callProcessing();
        $this->leadRepository->save($lead);
    }

    /**
     * @param int|Lead $lead
     * @param int|Employee $user
     * @param int|null $creatorId
     */
    public function take($lead, $user, $creatorId): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $user = $this->serviceFinder->userFind($user);

        EmployeeAccess::leadAccess($lead, $user);

        $this->guardUserIsCaller($user->id, $lead->id);
        $this->guardLeadForTake($lead);

        $lead->processing($user->id, $creatorId, 'Lead redial');
        $this->leadRepository->save($lead);
    }

    /**
     * @param int $userId
     * @param int $leadId
     */
    private function guardUserIsCaller(int $userId, int $leadId): void
    {
        $call = Call::find()
            ->andWhere(['c_created_user_id' => $userId])
            ->andWhere(['c_status_id' => [Call::STATUS_RINGING, Call::STATUS_IN_PROGRESS]])
            ->andWhere(['c_lead_id' => $leadId])
            ->exists();

        if (!$call) {
            throw new \DomainException('Current user is not caller!');
        }
    }

    /**
     * @param int $userId
     */
    private function guardUserFree(int $userId): void
    {
        $call = Call::find()
            ->andWhere(['c_created_user_id' => $userId])
            ->andWhere(['c_status_id' => [Call::STATUS_RINGING, Call::STATUS_IN_PROGRESS]])
            ->exists();

        if ($call) {
            throw new \DomainException('Current user cant call now!');
        }
    }

    /**
     * @param Lead $lead
     */
    private function guardLeadForTake(Lead $lead): void
    {
        if (!$lead->isPending()) {
            throw new \DomainException('Lead is not in status Pending');
        }
    }

    /**
     * @param Lead $lead
     */
    private function guardLeadForRedial(Lead $lead): void
    {
        if (!$lead->isPending()) {
            throw new \DomainException('Lead is not in status Pending');
        }

        if (!$lead->isCallReady()) {
            throw new \DomainException('Lead is not ready for call');
        }

        $leadQCall = LeadQcall::find()->andWhere(['lqc_lead_id' => $lead->id])->one();

        if (!$leadQCall) {
            throw new \DomainException('Lead is not exist in Lead Redial Queue');
        }

        if (strtotime(date('Y-m-d H:i:s')) < strtotime($leadQCall->lqc_dt_from)) {
            throw new \DomainException('Cant call before Date Time From');
        }
    }

}
