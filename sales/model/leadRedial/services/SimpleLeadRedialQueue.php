<?php

namespace sales\model\leadRedial\services;

use common\models\Lead;
use sales\model\phoneList\entity\PhoneList;
use sales\repositories\lead\LeadQcallRepository;
use sales\repositories\lead\LeadRepository;
use sales\services\lead\LeadRedialService;
use sales\services\lead\qcall\QCallService;

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
 * @property AgentPhone $agentPhone
 */
class SimpleLeadRedialQueue implements LeadRedialQueue
{
    private Leads $leads;
    private Reserver $reserver;
    private LeadRedialService $leadRedialService;
    private QCallService $qCallService;
    private LeadQcallRepository $leadQcallRepository;
    private LeadRepository $leadRepository;
    private ClientPhones $clientPhones;
    private AgentPhone $agentPhone;

    public function __construct(
        Leads $leads,
        Reserver $reserver,
        LeadRedialService $leadRedialService,
        QCallService $qCallService,
        LeadQcallRepository $leadQcallRepository,
        LeadRepository $leadRepository,
        ClientPhones $clientPhones,
        AgentPhone $agentPhone
    ) {
        $this->leads = $leads;
        $this->reserver = $reserver;
        $this->leadRedialService = $leadRedialService;
        $this->qCallService = $qCallService;
        $this->leadQcallRepository = $leadQcallRepository;
        $this->leadRepository = $leadRepository;
        $this->clientPhones = $clientPhones;
        $this->agentPhone = $agentPhone;
    }

    public function getCall(int $userId): ?RedialCall
    {
        $leads = $this->leads->getLeads($userId);

        foreach ($leads as $leadId) {
            $key = new Key($leadId);
            $isReserved = $this->reserver->reserve($key, $userId);
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

            $agentPhone = $this->agentPhone->findOrUpdatePhone($lead);
            if (!$agentPhone) {
                $this->reserver->reset($key);
                continue;
            }
            $agentPhoneId = $this->getAgentPhoneId($agentPhone, $lead->id);
            if (!$agentPhoneId) {
                $this->reserver->reset($key);
                continue;
            }

            $lead->callPrepare();
            $this->leadRepository->save($lead);

            // todo ?
//            $this->qCallService->resetReservation($leadQcall);

            return new RedialCall(
                $agentPhone,
                $agentPhoneId,
                $clientPhone,
                $lead->project_id,
                $lead->id
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
        $clientPhones = $this->clientPhones->getPhones($lead);
        if ($clientPhones) {
            return $clientPhones[0]->phone;
        }
        \Yii::error([
            'message' => 'Not found client phone',
            'leadId' => $lead->id,
        ], 'SimpleLeadRedialQueue');
        return null;
    }
}
