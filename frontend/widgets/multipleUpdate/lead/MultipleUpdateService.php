<?php

namespace frontend\widgets\multipleUpdate\lead;

use common\models\Employee;
use common\models\Lead;
use sales\services\lead\LeadStateService;
use sales\services\lead\qcall\Config;
use sales\services\lead\qcall\FindPhoneParams;
use sales\services\lead\qcall\FindWeightParams;
use sales\services\lead\qcall\QCallService;
use yii\bootstrap4\Html;

/**
 * Class LeadMultiUpdateService
 *
 * @property LeadStateService $leadStateService
 * @property QCallService $qCallService
 * @property array $report
 */
class MultipleUpdateService
{
    private $leadStateService;
    private $qCallService;
    private $report;

    public function __construct(LeadStateService $leadStateService, QCallService $qCallService)
    {
        $this->leadStateService = $leadStateService;
        $this->qCallService = $qCallService;
        $this->report = [];
    }

    public function update(MultipleUpdateForm $form): array
    {
        $creatorId = $form->authUserId();

        foreach ($form->ids as $leadId) {

            if (!$lead = Lead::findOne($leadId)) {
                $this->addMessage('Not found Lead: ' . $leadId);
                continue;
            }

            if (!$lead->isAvailableForMultiUpdate() && !$form->authUserIsAdmin()) {
                $this->addMessage('Lead: ' . $leadId . ' with status: ' . $lead->getStatusName() . ' is not available for MultiUpdate. Available only status: Processing, FollowUp, Hold, Trash, Snooze');
                continue;
            }

            if ($form->isRedialProcess()) {
                if ($form->authUserIsAdmin()) {
                    $this->redialProcess($form, $lead);
                }
                continue;
            }

            $oldOwnerId = $lead->employee_id;

            $newOwner = $this->getNewOwner($form->userId, $oldOwnerId);

            if ($form->statusId) {

                if ($form->isPending()) {
                    try {
                        $this->leadStateService->pending($lead, $newOwner->id, $creatorId, $form->message);
                        $this->addMessage($this->movedStateMessage($lead, 'Pending', $oldOwnerId, $newOwner->id, $newOwner->userName));
                    } catch (\DomainException $e) {
                        $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
                        \Yii::warning($e->getMessage(), 'lead\MultipleUpdateService:pending:LeadId:' . $lead->id);
                    }
                } elseif ($form->isProcessing()) {
                    try {
                        $this->leadStateService->processing($lead, $newOwner->id, $creatorId, $form->message);
                        $this->addMessage($this->movedStateMessage($lead, 'Processing', $oldOwnerId, $newOwner->id, $newOwner->userName));
                    } catch (\DomainException $e) {
                        $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
                        \Yii::warning($e->getMessage(), 'lead\MultipleUpdateService:processing:LeadId:' . $lead->id);
                    }
                } elseif ($form->isReject()) {
                    try {
                        $this->leadStateService->reject($lead, $newOwner->id, $creatorId, $form->message);
                        $this->addMessage($this->movedStateMessage($lead, 'Reject', $oldOwnerId, $newOwner->id, $newOwner->userName));
                    } catch (\DomainException $e) {
                        $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
                        \Yii::warning($e->getMessage(), 'lead\MultipleUpdateService:reject:LeadId:' . $lead->id);
                    }
                } elseif ($form->isFollowUp()) {
                    try {
                        $this->leadStateService->followUp($lead, $newOwner->id, $creatorId, $form->message);
                        $this->addMessage($this->movedStateMessage($lead, 'Follow Up', $oldOwnerId, $newOwner->id, $newOwner->userName));
                    } catch (\DomainException $e) {
                        $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
                        \Yii::warning($e->getMessage(), 'lead\MultipleUpdateService:followUp:LeadId:' . $lead->id);
                    }
                } elseif ($form->isSold()) {
                    try {
                        $this->leadStateService->sold($lead, $newOwner->id, $creatorId, $form->message);
                        $this->addMessage($this->movedStateMessage($lead, 'Sold', $oldOwnerId, $newOwner->id, $newOwner->userName));
                    } catch (\DomainException $e) {
                        $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
                        \Yii::warning($e->getMessage(), 'lead\MultipleUpdateService:sold:LeadId:' . $lead->id);
                    }
                } elseif ($form->isTrash()) {
                    try {
                        $this->leadStateService->trash($lead, $newOwner->id, $creatorId, $form->message);
                        $this->addMessage($this->movedStateMessage($lead, 'Trash', $oldOwnerId, $newOwner->id, $newOwner->userName));
                    } catch (\DomainException $e) {
                        $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
                        \Yii::warning($e->getMessage(), 'lead\MultipleUpdateService:trash:LeadId:' . $lead->id);
                    }
                } elseif ($form->isBooked()) {
                    try {
                        $this->leadStateService->booked($lead, $newOwner->id, $creatorId, $form->message);
                        $this->addMessage($this->movedStateMessage($lead, 'Booked', $oldOwnerId, $newOwner->id, $newOwner->userName));
                    } catch (\DomainException $e) {
                        $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
                        \Yii::warning($e->getMessage(), 'lead\MultipleUpdateService:booked:LeadId:' . $lead->id);
                    }
                } elseif ($form->isSnooze()) {
                    try {
                        $this->leadStateService->snooze($lead, $newOwner->id, '', $creatorId, $form->message);
                        $this->addMessage($this->movedStateMessage($lead, 'Snooze', $oldOwnerId, $newOwner->id, $newOwner->userName));
                    } catch (\DomainException $e) {
                        $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
                        \Yii::warning($e->getMessage(), 'lead\MultipleUpdateService:snooze:LeadId:' . $lead->id);
                    }
                } else {
                    $this->addMessage('Undefined status: ' . $form->statusId . ' for multi update Lead: ' . $lead->id);
                    \Yii::warning('Undefined status: ' . $form->statusId . ' for multi update Lead: ' . $lead->id, 'lead\MultipleUpdateService:undefinedStatus:LeadId:' . $lead->id);
                }

            } elseif ($form->userId) {

                if ($lead->isPending()) {
                    try {
                        $this->leadStateService->pending($lead, $newOwner->id, $creatorId, $form->message);
                        $this->addMessage($this->changeOwnerMessage($lead, $newOwner->userName));
                    } catch (\DomainException $e) {
                        $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
                        \Yii::warning($e->getMessage(), 'lead\MultipleUpdateService:pending:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isProcessing()) {
                    try {
                        $this->leadStateService->processing($lead, $newOwner->id, $creatorId, $form->message);
                        $this->addMessage($this->changeOwnerMessage($lead, $newOwner->userName));
                    } catch (\DomainException $e) {
                        $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
                        \Yii::warning($e->getMessage(), 'lead\MultipleUpdateService:processing:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isReject()) {
                    try {
                        $this->leadStateService->reject($lead, $newOwner->id, $creatorId, $form->message);
                        $this->addMessage($this->changeOwnerMessage($lead, $newOwner->userName));
                    } catch (\DomainException $e) {
                        $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
                        \Yii::warning($e->getMessage(), 'lead\MultipleUpdateService:reject:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isFollowUp()) {
                    try {
                        $this->leadStateService->followUp($lead, $newOwner->id, $creatorId, $form->message);
                        $this->addMessage($this->changeOwnerMessage($lead, $newOwner->userName));
                    } catch (\DomainException $e) {
                        $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
                        \Yii::warning($e->getMessage(), 'lead\MultipleUpdateService:followUp:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isSold()) {
                    try {
                        $this->leadStateService->sold($lead, $newOwner->id, $creatorId, $form->message);
                        $this->addMessage($this->changeOwnerMessage($lead, $newOwner->userName));
                    } catch (\DomainException $e) {
                        $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
                        \Yii::warning($e->getMessage(), 'lead\MultipleUpdateService:sold:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isTrash()) {
                    try {
                        $this->leadStateService->trash($lead, $newOwner->id, $creatorId, $form->message);
                        $this->addMessage($this->changeOwnerMessage($lead, $newOwner->userName));
                    } catch (\DomainException $e) {
                        $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
                        \Yii::warning($e->getMessage(), 'lead\MultipleUpdateService:trash:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isBooked()) {
                    try {
                        $this->leadStateService->booked($lead, $newOwner->id, $creatorId, $form->message);
                        $this->addMessage($this->changeOwnerMessage($lead, $newOwner->userName));
                    } catch (\DomainException $e) {
                        $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
                        \Yii::warning($e->getMessage(), 'lead\MultipleUpdateService:booked:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isSnooze()) {
                    try {
                        $this->leadStateService->snooze($lead, $newOwner->id, '', $creatorId, $form->message);
                        $this->addMessage($this->changeOwnerMessage($lead, $newOwner->userName));
                    } catch (\DomainException $e) {
                        $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
                        \Yii::warning($e->getMessage(), 'lead\MultipleUpdateService:snooze:LeadId:' . $lead->id);
                    }
                }

            } else {
                \Yii::warning('Undefined action for multi update ', 'lead\MultipleUpdateService:undefinedStatus:LeadId:' . $lead->id);
            }

        }
        return $this->report;
    }

    public function formatReport(array $reports): string
    {
        if (!$reports) {
            return '';
        }

        $out = '<div class="card" style="margin-bottom: 10px" ><div class="card-body"><ul>';
        foreach ($reports as $report) {
            $out .= Html::tag('li', Html::tag('span', $report, ['style' => 'color: #28a048']));
        }
        return $out . '</ul></div></div>';
    }


    private function addMessage(string $message): void
    {
        $this->report[] = $message;
    }

    /**
     * @param $newOwnerId
     * @param int|null $oldOwnerId
     * @return NewOwner
     */
    private function getNewOwner(?int $newOwnerId, ?int $oldOwnerId): NewOwner
    {
        if ($newOwnerId === -1) {
            return new NewOwner(null, null);
        }

        $ownerId = $newOwnerId ?? $oldOwnerId;

        return new NewOwner($ownerId, $this->getUserName($ownerId));
    }

    /**
     * @param Lead $lead
     * @param string $status
     * @param int|null $oldOwner
     * @param int|null $newOwner
     * @param string $newOwnerUserName
     * @return string
     */
    private function movedStateMessage(Lead $lead, string $status, ?int $oldOwner, ?int $newOwner, $newOwnerUserName): string
    {
        $message = '<span style="color: #28a048">Lead: ' . $lead->id . ' moved to ' . $status;
        if ($newOwner && $newOwner !== $oldOwner) {
            $message .= ' with new Owner : ' . $newOwnerUserName;
        } elseif (!$newOwner && $oldOwner) {
            $message .= ' without new Owner';
        }
        return $message . '</span>';
    }

    /**
     * @param Lead $lead
     * @param string $newOwnerUserName
     * @return string
     */
    private function changeOwnerMessage(Lead $lead, $newOwnerUserName): string
    {
        if (!$newOwnerUserName) {
            return  '<span style="color: #28a048">Lead: ' . $lead->id . ' removed owner </span>';
        }
        return  '<span style="color: #28a048">Lead: ' . $lead->id . ' changed owner to ' . $newOwnerUserName . '</span>';
    }

    /**
     * @param int|null $id
     * @return string
     */
    private function getUserName(?int $id): string
    {
        if ($id === null) {
            return '';
        }
        if ($user = Employee::findOne($id)) {
            return $user->username;
        }
        return 'Undefined username';
    }

    private function redialProcess(MultipleUpdateForm $form, Lead $lead): void
    {
        $message = '';
        if ($form->isRedialAdd()) {
            if ($this->qCallService->isExist($lead->id)) {
                $message = 'Lead: ' . $lead->id . ' already exist on Qcall List';
            } else {
                try {
                    $qCall = $this->qCallService->create(
                        $lead->id,
                        new Config($lead->status, $lead->getCountOutCallsLastFlow()),
                        new FindWeightParams($lead->project_id, $lead->status),
                        $lead->offset_gmt,
                        new FindPhoneParams($lead->project_id, $lead->l_dep_id)
                    );
                    if ($qCall) {
                        $message = 'Lead: ' . $lead->id . ' added to Qcall List';
                    } else {
                        $message = 'Lead: ' . $lead->id . ' not added to Qcall List';
                    }
                } catch (\Throwable $e) {
                    $message = 'Lead: ' . $lead->id . ' added to Qcall List error';
                }
            }
        } elseif ($form->isRedialRemove()) {
            if ($this->qCallService->isExist($lead->id)) {
                try {
                    $this->qCallService->remove($lead->id);
                    $message = 'Lead: ' . $lead->id . ' was removed from Qcall List';
                } catch (\Throwable $e) {
                    $message = 'Lead: ' . $lead->id . ' removed from Qcall List error';
                }
            } else {
                $message = 'Lead: ' . $lead->id . ' not found on Qcall List';
            }
        }
        $this->addMessage($message);
    }
}
