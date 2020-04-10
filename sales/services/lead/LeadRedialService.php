<?php

namespace sales\services\lead;

use Yii;
use common\models\Call;
use common\models\DepartmentPhoneProject;
use common\models\Employee;
use common\models\Lead;
use common\models\LeadFlow;
use common\models\LeadQcall;
use common\models\Project;
use common\models\search\LeadQcallSearch;
use sales\access\EmployeeAccess;
use sales\guards\lead\TakeGuard;
use sales\repositories\lead\LeadRepository;
use sales\services\lead\qcall\Config;
use sales\services\lead\qcall\FindPhoneParams;
use sales\services\lead\qcall\QCallService;
use sales\services\ServiceFinder;
use sales\services\TransactionManager;
use yii\helpers\VarDumper;

/**
 * Class LeadRedialService
 *
 * @property LeadRepository $leadRepository
 * @property ServiceFinder $serviceFinder
 * @property TransactionManager $transactionManager
 * @property TakeGuard $takeGuard
 * @property QCallService $qCallService
 */
class LeadRedialService
{

    private $leadRepository;
    private $serviceFinder;
    private $transactionManager;
    private $takeGuard;
    private $qCallService;

    public function __construct(
        LeadRepository $leadRepository,
        ServiceFinder $serviceFinder,
        TransactionManager $transactionManager,
        TakeGuard $takeGuard,
        QCallService $qCallService
    )
    {
        $this->leadRepository = $leadRepository;
        $this->serviceFinder = $serviceFinder;
        $this->transactionManager = $transactionManager;
        $this->takeGuard = $takeGuard;
        $this->qCallService = $qCallService;
    }

    /**
     * @param $lead
     * @param $user
     * @throws \Throwable
     */
    public function reservationBeforeCall($lead, $user): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $user = $this->serviceFinder->userFind($user);

        EmployeeAccess::leadAccess($lead, $user);

        $this->guardUserFree($user);
        $this->guardLeadForCall($lead, $user);

        $this->transactionManager->wrap(function () use ($lead) {

            $lead->callPrepare();
            $this->leadRepository->save($lead);

            if ($qCall = $lead->leadQcall) {
                $this->qCallService->resetReservation($qCall);
            }

        });
    }

    /**
     * @param $lead
     * @param $user
     * @throws \Throwable
     */
    public function redial($lead, $user): void
    {
        $lead = $this->serviceFinder->leadFind($lead);
        $user = $this->serviceFinder->userFind($user);

        EmployeeAccess::leadAccess($lead, $user);

        $this->guardUserFree($user);
        $this->guardLeadForCall($lead, $user);

        if ($qCall = $lead->leadQcall) {
            $this->transactionManager->wrap(function () use ($qCall, $user) {
                $this->qCallService->resetOldReservationByUser($user->id);
                $this->qCallService->reservation($qCall, $user->id);
            });
        }
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
     * @param int|Lead $lead
     * @param int|Employee $user
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
            $this->qCallService->remove($lead->id);
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
        if (!$lead->isPending()) {
            throw new \DomainException('Lead is not in status Pending');
        }
    }

    /**
     * @param Lead $lead
     * @param Employee $user
     */
    private function guardLeadForCall(Lead $lead, Employee $user): void
    {
        if (!$user->isAdmin() && !$lead->isPending()) {
            throw new \DomainException('Lead is not in status Pending');
        }

        if (!$user->isAdmin() && ($lead->isCallProcessing() || $lead->isCallPrepare())) {
            throw new \DomainException('Lead is not ready for call');
        }

        $leadQCall = LeadQcall::find()->andWhere(['lqc_lead_id' => $lead->id])->one();

        if (!$leadQCall) {
            throw new \DomainException('Lead is not exist in Lead Redial Queue');
        }

        if (strtotime(date('Y-m-d H:i:s')) < strtotime($leadQCall->lqc_dt_from)) {
            throw new \DomainException('Cant call before Date Time From');
        }

//        if (LeadQcall::find()->isUserReservedOtherLead($user->id, $lead->id)) {
//            throw new \DomainException('You already reserved one Lead. Try again later.');
//        }

        if ($leadQCall->isReserved() && !$leadQCall->isReservationUser($user->id)) {
            throw new \DomainException('Lead reserved. Try again later.');
        }

        if ((bool)\Yii::$app->params['settings']['enable_take_frequency_minutes']) {
            $flowDescriptions = self::getFlowDescriptions();
            $this->takeGuard->frequencyMinutesGuard($user, $flowDescriptions);
        }

        if ((bool)\Yii::$app->params['settings']['enable_min_percent_take_leads']) {
            $flowDescriptions = self::getFlowDescriptions();
            $this->takeGuard->minPercentGuard($user, $flowDescriptions);
        }

        if ((bool)\Yii::$app->params['settings']['enable_redial_shift_time_limits']) {
            $this->takeGuard->shiftTimeGuard($user);
        }
    }

    /**
     * @param Lead $lead
     * @param Employee $user
     */
    private function guardLeadForCallFromLastCalls(Lead $lead, Employee $user): void
    {
        if (!$user->isAdmin() && !$lead->isPending()) {
            throw new \DomainException('Lead is not in status Pending');
        }

        if (!$user->isAdmin() && ($lead->isCallProcessing() || $lead->isCallPrepare())) {
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

    /**
     * @return array
     */
    public static function getFlowDescriptions(): array
    {
        $flowDescriptions = [];
        if ((bool)\Yii::$app->params['settings']['count_taken_leads_created_manually']) {
            $flowDescriptions[] = LeadFlow::DESCRIPTION_MANUAL_CREATE;
        }
        if ((bool)\Yii::$app->params['settings']['count_taken_leads_incoming_call']) {
            $flowDescriptions[] = LeadFlow::DESCRIPTION_CALL_AUTO_CREATED_LEAD;
        }
        return $flowDescriptions;
    }

    /**
     * @param Lead $lead
     * @return string
     */
    public function findOrUpdatePhoneNumberFrom(Lead $lead): string
    {
        if (
            ($qCall = $lead->leadQcall)
            && ($phoneFrom = $qCall->lqc_call_from)
            && DepartmentPhoneProject::find()->enabled()->redial()->byPhone($phoneFrom, false)->exists()
        ) {
            return $phoneFrom;
        }

        if ($qCall) {
            try {
                $phoneFrom = (Yii::createObject(QCallService::class))->updateCallFrom(
                    $qCall,
                    new Config($lead->status, $lead->getCountOutCallsLastFlow()),
                    new FindPhoneParams($lead->project_id, $lead->l_dep_id)
                );
                if ($phoneFrom) {
                    return $phoneFrom;
                }
            } catch (\Throwable $e) {
                Yii::error($e, 'LeadRedialService:findPhoneNumberFrom:QCallService:updateCallFrom');
            }
        }

//        if (($phone = Project::findOne($lead->project_id)) && $phone->contactInfo->phone) {
//            return $phone->contactInfo->phone;
//        }

        throw new \DomainException('Not found phoneFrom. Please, contact to administrator.');
    }
}
