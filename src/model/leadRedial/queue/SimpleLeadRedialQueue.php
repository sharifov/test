<?php

namespace src\model\leadRedial\queue;

use common\models\Employee;
use common\models\Lead;
use common\models\PhoneBlacklist;
use src\model\leadRedial\job\LeadCallPrepareCheckerJob;
use src\model\phoneList\entity\PhoneList;
use src\repositories\lead\LeadQcallRepository;
use src\repositories\lead\LeadRepository;
use src\services\lead\LeadRedialService;
use src\services\lead\qcall\QCallService;
use src\services\TransactionManager;

/**
 * Class SimpleLeadRedialQueue
 *
 * @property Leads $leads
 * @property Reserver $reserver
 * @property LeadRedialService $leadRedialService
 * @property QCallService $qCallService
 * @property LeadQcallRepository $leadQcallRepository
 * @property LeadRepository $leadRepository
 * @property ClientPhones $clientPhones
 * @property TransactionManager $transactionManager
 */
class SimpleLeadRedialQueue implements LeadRedialQueue
{
    private const LEAD_PREPARE_DELAY = 30;

    private Leads $leads;
    private Reserver $reserver;
    private LeadRedialService $leadRedialService;
    private QCallService $qCallService;
    private LeadQcallRepository $leadQcallRepository;
    private LeadRepository $leadRepository;
    private ClientPhones $clientPhones;
    private TransactionManager $transactionManager;

    public function __construct(
        Leads $leads,
        Reserver $reserver,
        LeadRedialService $leadRedialService,
        QCallService $qCallService,
        LeadQcallRepository $leadQcallRepository,
        LeadRepository $leadRepository,
        ClientPhones $clientPhones,
        TransactionManager $transactionManager
    ) {
        $this->leads = $leads;
        $this->reserver = $reserver;
        $this->leadRedialService = $leadRedialService;
        $this->qCallService = $qCallService;
        $this->leadQcallRepository = $leadQcallRepository;
        $this->leadRepository = $leadRepository;
        $this->clientPhones = $clientPhones;
        $this->transactionManager = $transactionManager;
    }

    public function getCall(Employee $user): ?RedialCall
    {
        $leads = $this->leads->getLeads($user);

        foreach ($leads as $leadId) {
            $key = new Key($leadId);
            $isReserved = $this->reserver->reserve($key, $user->id);
            if (!$isReserved) {
                continue;
            }

            $leadQcall = $this->leadQcallRepository->find($leadId);
            $lead = $leadQcall->lqcLead;

            $clientPhone = $this->getClientPhone($lead);
            if (!$clientPhone) {
                $this->reserver->reset($key);
                continue;
            }

            $agentPhone = $leadQcall->lqc_call_from;
            if (!$agentPhone) {
                $this->reserver->reset($key);
                continue;
            }
            $agentPhoneId = $this->getAgentPhoneId($agentPhone, $lead->id);
            if (!$agentPhoneId) {
                $this->reserver->reset($key);
                continue;
            }

            $this->transactionManager->wrap(function () use ($lead, $leadQcall) {
                $lead->callPrepare();
                $this->leadRepository->save($lead);
                $this->qCallService->resetReservation($leadQcall);
            });

            $job = new LeadCallPrepareCheckerJob($lead->id);
            $delay = self::LEAD_PREPARE_DELAY;
            $job->delayJob = $delay;
            \Yii::$app->queue_job->delay($delay)->push($job);

            return new RedialCall(
                $user->id,
                $agentPhone,
                $agentPhoneId,
                $clientPhone,
                $lead->project_id,
                $lead->project->name,
                $lead->l_dep_id,
                $lead->l_dep_id ? $lead->lDep->dep_name : '',
                $lead->id,
                $lead->client_id,
                $lead->client_id ? $lead->client->getShortName() : 'ClientName',
                $lead->client_id && $lead->client->isClient(),
            );
        }

        return null;
    }

    private function getAgentPhoneId(string $phone, int $leadId): ?int
    {
        $phoneListId = PhoneList::find()->select(['pl_id'])->andWhere(['pl_phone_number' => $phone])->scalar();
        if ($phoneListId) {
            return (int)$phoneListId;
        }
        \Yii::error([
            'message' => 'Not found agent phone list',
            'leadId' => $leadId,
            'phone' => $phone,
        ], 'SimpleLeadRedialQueue');
        return null;
    }

    private function getClientPhone(Lead $lead): ?string
    {
        $clientPhone = $this->clientPhones->getFirstClientPhone($lead);

        if (!$clientPhone) {
            \Yii::error([
                'message' => 'Not found client phone',
                'leadId' => $lead->id,
            ], 'SimpleLeadRedialQueue');
            return null;
        }

        if (PhoneBlacklist::find()->isExists($clientPhone->phone)) {
            try {
                $this->qCallService->remove($lead->id);
                \Yii::warning([
                    'message' => 'Lead removed from Redial Queue, because found blocked client phone',
                    'phone' => $clientPhone->phone,
                    'leadId' => $lead->id,
                ], 'SimpleLeadRedialQueue:getClientPhone');
            } catch (\Throwable $e) {
                \Yii::error([
                    'message' => 'Removed lead with blocked client phone from Redial Queue error',
                    'exception' => $e->getMessage(),
                    'phone' => $clientPhone->phone,
                    'leadId' => $lead->id,
                ], 'SimpleLeadRedialQueue:getClientPhone');
            }
            return null;
        }

        return $clientPhone->phone;
    }
}
