<?php

namespace sales\model\leadRedial\services;

use common\models\Lead;
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
 */
class SimpleLeadRedialQueue implements LeadRedialQueue
{
    private Leads $leads;
    private Reserver $reserver;
    private LeadRedialService $leadRedialService;
    private QCallService $qCallService;
    private LeadQcallRepository $leadQcallRepository;
    private LeadRepository $leadRepository;

    public function __construct(
        Leads $leads,
        Reserver $reserver,
        LeadRedialService $leadRedialService,
        QCallService $qCallService,
        LeadQcallRepository $leadQcallRepository,
        LeadRepository $leadRepository
    ) {
        $this->leads = $leads;
        $this->reserver = $reserver;
        $this->leadRedialService = $leadRedialService;
        $this->qCallService = $qCallService;
        $this->leadQcallRepository = $leadQcallRepository;
        $this->leadRepository = $leadRepository;
    }

    public function getCall(int $userId): ?RedialCall
    {
        $leads = $this->leads->getLeads($userId);
        foreach ($leads as $leadId) {
            $isReserved = $this->reserver->reserve(new Key($leadId), $userId);
            if (!$isReserved) {
                continue;
            }
            $leadQcall = $this->leadQcallRepository->find($leadId);

            $lead = $leadQcall->lqcLead;
            $lead->callPrepare();
            $this->leadRepository->save($lead);

            // todo ?
//            $this->qCallService->resetReservation($leadQcall);
        }
        return null;
    }

    private function getClientPhone(Lead $lead): string
    {
        // todo
    }
}
