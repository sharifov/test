<?php

namespace sales\services\lead;

use common\models\Lead;
use common\models\Note;
use common\models\Reason;
use sales\repositories\reason\ReasonRepository;
use sales\repositories\lead\LeadRepository;
use sales\repositories\note\NoteRepository;
use sales\repositories\user\UserRepository;
use sales\services\TransactionManager;

/**
 * Class LeadUnassignService
 * @property LeadRepository $leadRepository
 * @property UserRepository $userRepository
 * @property ReasonRepository $reasonRepository
 * @property TransactionManager $transactionManager
 * @property NoteRepository $noteRepository
 */
class LeadUnassignService
{
    private $leadRepository;
    private $userRepository;
    private $reasonRepository;
    private $noteRepository;

    public function __construct(
        LeadRepository $leadRepository,
        UserRepository $userRepository,
        ReasonRepository $reasonRepository,
        TransactionManager $transactionManager,
        NoteRepository $noteRepository
    )
    {
        $this->leadRepository = $leadRepository;
        $this->userRepository = $userRepository;
        $this->reasonRepository = $reasonRepository;
        $this->transactionManager = $transactionManager;
        $this->noteRepository = $noteRepository;
    }

    public function unassign(Lead $lead, Reason $reason, int $userId, $snoozeFor, $agent)
    {

        $url = $this->transactionManager->wrap(function () use ($lead, $reason, $userId, $snoozeFor, $agent) {

            $reason->setOwner($userId, $lead->id);
            $this->reasonRepository->save($reason);

            $url = [];

            switch ($reason->queue) {
                case 'follow-up':
                    $lead->followUp();
                    $url = ['follow-up'];
                    break;
                case 'trash':
                    if ($reason->duplicateLeadId) {
                        $lead->setDuplicate($reason->duplicateLeadId);
                    } else {
                        $lead->trash();
                    }
                    break;
                case 'snooze':
                    $lead->snooze($snoozeFor);
                    break;
                case 'return':
                    if ($reason->returnToQueue === 'follow-up') {
                        $lead->followUp();
                    } elseif ($agent !== null) {
                        $agent = (int)$agent;
                        $user = $this->userRepository->find($agent);
                        $lead->processing($user->id);
                    }
                    break;
                case 'processing-over':
                    $oldOwnerUsername = $lead->employee ? $lead->employee->username : '-';
                    $lead->takeOver($reason->employee_id);
                    $note = Note::create($userId, $lead->id, 'Take Over in PROCESSING status.<br>Reason: ' . $reason->reason . '<br>Last Agent: ' . $oldOwnerUsername);
                    $this->noteRepository->save($note);
                    $url = ['lead/view', 'gid' => $lead->gid];
                    break;
                case 'reject':
                    $lead->reject();
                    $url = ['trash'];
                    break;
            }
            $this->leadRepository->save($lead);

            return $url;

        });

        return $url;
    }


}