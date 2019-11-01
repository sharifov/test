<?php

namespace sales\services\lead;

use common\models\Call;
use common\models\Employee;
use common\models\Lead;
use common\models\LeadQcall;
use common\models\search\LeadQcallSearch;
use sales\access\EmployeeAccess;
use sales\repositories\lead\LeadRepository;
use sales\services\ServiceFinder;
use sales\services\TransactionManager;
use yii\helpers\VarDumper;

/**
 * Class LeadRedialService
 *
 * @property LeadRepository $leadRepository
 * @property ServiceFinder $serviceFinder
 * @property TransactionManager $transactionManager
 */
class LeadRedialService
{

    private $leadRepository;
    private $serviceFinder;
    private $transactionManager;

    public function __construct(
        LeadRepository $leadRepository,
        ServiceFinder $serviceFinder,
        TransactionManager $transactionManager
    )
    {
        $this->leadRepository = $leadRepository;
        $this->serviceFinder = $serviceFinder;
        $this->transactionManager = $transactionManager;
    }

    /**
     * @param int|Lead $lead
     * @param int|Employee $user
     */
    public function reservation($lead, $user): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $user = $this->serviceFinder->userFind($user);

        EmployeeAccess::leadAccess($lead, $user);

        $this->guardUserFree($user);
        $this->guardLeadForCall($lead);

        $lead->callPrepare();
        $this->leadRepository->save($lead);
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

        $this->guardUserFree($user);
        $this->guardLeadForCall($lead);
    }

    /**
     * @param int|Lead $lead
     * @param int|Employee $user
     */
    public function reservationFromLastCalls($lead, $user): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $user = $this->serviceFinder->userFind($user);

        EmployeeAccess::leadAccess($lead, $user);

        $this->guardUserFree($user);
        $this->guardLeadForCallFromLastCalls($lead, $user);

        $lead->callPrepare();
        $this->leadRepository->save($lead);
    }

    /**
     * @param int|Lead $lead
     * @param int|Employee $user
     */
    public function redialFromLastCalls($lead, $user): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $user = $this->serviceFinder->userFind($user);

        EmployeeAccess::leadAccess($lead, $user);

        $this->guardUserFree($user);
        $this->guardLeadForCallFromLastCalls($lead, $user);
    }

    /**
     * @param $lead
     * @param $user
     * @param $creatorId
     * @throws \Throwable
     */
    public function take($lead, $user, $creatorId): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $user = $this->serviceFinder->userFind($user);

        EmployeeAccess::leadAccess($lead, $user);

        $this->guardUserIsCaller($user->id, $lead->id);
        $this->guardLeadForTake($lead);

        $lead->answered();
        $lead->processing($user->id, $creatorId, 'Lead redial');

        $this->transactionManager->wrap(function () use ($lead) {
            if ($qCall = LeadQcall::find()->andWhere(['lqc_lead_id' => $lead->id])->one()) {
                $qCall->delete();
            }
            $this->leadRepository->save($lead);
        });
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
     * @param Employee $user
     */
    private function guardUserFree($user): void
    {
        if (!$user->isCallFree()) {
            throw new \DomainException('Current user cant call now! He is busy');
        }
    }

    /**
     * @param Lead $lead
     */
    private function guardLeadForTake(Lead $lead): void
    {
//        if (!$lead->isPending()) {
//            throw new \DomainException('Lead is not in status Pending');
//        }
    }

    /**
     * @param Lead $lead
     */
    private function guardLeadForCall(Lead $lead): void
    {
//        if (!$lead->isPending()) {
//            throw new \DomainException('Lead is not in status Pending');
//        }

//        if (!$lead->isCallReady()) {
        if ($lead->isCallProcessing()) {
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

    /**
     * @param Lead $lead
     * @param Employee $user
     */
    private function guardLeadForCallFromLastCalls(Lead $lead, Employee $user): void
    {
//        if (!$lead->isPending()) {
//            throw new \DomainException('Lead is not in status Pending');
//        }

//        if (!$lead->isCallReady()) {
        if ($lead->isCallProcessing()) {
            throw new \DomainException('Lead is not ready for call');
        }

        $leadQCall = LeadQcall::find()->andWhere(['lqc_lead_id' => $lead->id])->one();

        if (!$leadQCall) {
            throw new \DomainException('Lead is not exist in Lead Redial Queue');
        }

        $leadIds = array_keys((new LeadQcallSearch())->searchLastCalls([], $user)->query->indexBy('lqc_lead_id')->asArray()->all());
        if (!in_array($lead->id, $leadIds, true)) {
            throw new \DomainException('Lead is not exist on last dialed leads');
        }
    }

}
