<?php

namespace sales\services\lead;

use common\models\Employee;
use common\models\Lead;
use common\models\Reason;
use frontend\models\LeadMultipleForm;
use sales\services\lead\qcall\Config;
use sales\services\lead\qcall\FindPhoneParams;
use sales\services\lead\qcall\FindWeightParams;
use sales\services\lead\qcall\QCallService;

/**
 * Class LeadMultiUpdateService
 *
 * @property LeadStateService $leadStateService
 * @property QCallService $qCallService
 */
class LeadMultiUpdateService
{
    private $leadStateService;
    private $qCallService;

    public function __construct(LeadStateService $leadStateService, QCallService $qCallService)
    {
        $this->leadStateService = $leadStateService;
        $this->qCallService = $qCallService;
    }

    public function update(LeadMultipleForm $multipleForm, Employee $user): array
    {
        $report = [];
        $creatorId = $user->id;

        foreach ($multipleForm->lead_list as $lead_id) {

            $leadId = (int)$lead_id;

            if (!$lead = Lead::findOne($leadId)) {
                $report[] = 'Not found Lead: ' . $leadId;
                continue;
            }

            if ($multipleForm->isRedialProcess()) {
                if ($user->isAdmin()) {
                    $report[] = $this->redialProcess($multipleForm, $lead);
                }
                continue;
            }

            if (!$lead->isAvailableForMultiUpdate() && !\Yii::$app->user->identity->isAdmin()) {
                $report[] = 'Lead: ' . $leadId . ' with status: ' . $lead->getStatusName() . ' is not available for MultiUpdate. Available only status: Processing, FollowUp, Hold, Trash, Snooze';
                continue;
            }

            $reason = null;

            if ($multipleForm->status_id && is_numeric($multipleForm->reason_id)) {
                if ($multipleForm->reason_id > 0) {
                    $reason = Reason::getReasonByStatus($multipleForm->status_id, $multipleForm->reason_id);
                } else {
                    $reason = $multipleForm->reason_description;
                }
            }

            $newOwnerUserName = '';
            if ($newOwnerId = $this->getNewOwner($multipleForm->employee_id, $lead)) {
                $newOwnerUserName = $this->getUserName($newOwnerId);
            }

            $oldOwner = $lead->employee_id;

            if ($multipleForm->status_id) {

                if ($multipleForm->status_id == Lead::STATUS_PENDING) {
                    try {
                        $this->leadStateService->pending($lead, $newOwnerId, $creatorId, $reason);
                        $report[] = $this->movedStateMessage($lead, 'Pending', $oldOwner, $newOwnerId, $newOwnerUserName);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:pending:LeadId:' . $lead->id);
                    }
                } elseif ($multipleForm->status_id == Lead::STATUS_PROCESSING) {
                    try {
                        $this->leadStateService->processing($lead, $newOwnerId, $creatorId, $reason);
                        $report[] = $this->movedStateMessage($lead, 'Processing', $oldOwner, $newOwnerId, $newOwnerUserName);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:processing:LeadId:' . $lead->id);
                    }
                } elseif ($multipleForm->status_id == Lead::STATUS_REJECT) {
                    try {
                        $this->leadStateService->reject($lead, $newOwnerId, $creatorId, $reason);
                        $report[] = $this->movedStateMessage($lead, 'Reject', $oldOwner, $newOwnerId, $newOwnerUserName);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:reject:LeadId:' . $lead->id);
                    }
                } elseif ($multipleForm->status_id == Lead::STATUS_FOLLOW_UP) {
                    try {
                        $this->leadStateService->followUp($lead, $newOwnerId, $creatorId, $reason);
                        $report[] = $this->movedStateMessage($lead, 'Follow Up', $oldOwner, $newOwnerId, $newOwnerUserName);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:followUp:LeadId:' . $lead->id);
                    }
                } elseif ($multipleForm->status_id == Lead::STATUS_SOLD) {
                    try {
                        $this->leadStateService->sold($lead, $newOwnerId, $creatorId, $reason);
                        $report[] = $this->movedStateMessage($lead, 'Sold', $oldOwner, $newOwnerId, $newOwnerUserName);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:sold:LeadId:' . $lead->id);
                    }
                } elseif ($multipleForm->status_id == Lead::STATUS_TRASH) {
                    try {
                        $this->leadStateService->trash($lead, $newOwnerId, $creatorId, $reason);
                        $report[] = $this->movedStateMessage($lead, 'Trash', $oldOwner, $newOwnerId, $newOwnerUserName);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:trash:LeadId:' . $lead->id);
                    }
                } elseif ($multipleForm->status_id == Lead::STATUS_BOOKED) {
                    try {
                        $this->leadStateService->booked($lead, $newOwnerId, $creatorId, $reason);
                        $report[] = $this->movedStateMessage($lead, 'Booked', $oldOwner, $newOwnerId, $newOwnerUserName);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:booked:LeadId:' . $lead->id);
                    }
                } elseif ($multipleForm->status_id == Lead::STATUS_SNOOZE) {
                    try {
                        $this->leadStateService->snooze($lead, $newOwnerId, '', $creatorId, $reason);
                        $report[] = $this->movedStateMessage($lead, 'Snooze', $oldOwner, $newOwnerId, $newOwnerUserName);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:snooze:LeadId:' . $lead->id);
                    }
                } else {
                    \Yii::warning('Undefined status: ' . $multipleForm->status_id . ' for multi update Lead: ' . $lead->id, 'LeadMultiUpdateService:undefinedStatus:LeadId:' . $lead->id);
                }

            } elseif ($multipleForm->employee_id) {

                if ($lead->isPending()) {
                    try {
                        $this->leadStateService->pending($lead, $newOwnerId, $creatorId, $reason);
                        $report[] = $this->changeOwnerMessage($lead, $newOwnerUserName);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:pending:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isProcessing()) {
                    try {
                        $this->leadStateService->processing($lead, $newOwnerId, $creatorId, $reason);
                        $report[] = $this->changeOwnerMessage($lead, $newOwnerUserName);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:processing:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isReject()) {
                    try {
                        $this->leadStateService->reject($lead, $newOwnerId, $creatorId, $reason);
                        $report[] = $this->changeOwnerMessage($lead, $newOwnerUserName);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:reject:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isFollowUp()) {
                    try {
                        $this->leadStateService->followUp($lead, $newOwnerId, $creatorId, $reason);
                        $report[] = $this->changeOwnerMessage($lead, $newOwnerUserName);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:followUp:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isSold()) {
                    try {
                        $this->leadStateService->sold($lead, $newOwnerId, $creatorId, $reason);
                        $report[] = $this->changeOwnerMessage($lead, $newOwnerUserName);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:sold:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isTrash()) {
                    try {
                        $this->leadStateService->trash($lead, $newOwnerId, $creatorId, $reason);
                        $report[] = $this->changeOwnerMessage($lead, $newOwnerUserName);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:trash:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isBooked()) {
                    try {
                        $this->leadStateService->booked($lead, $newOwnerId, $creatorId, $reason);
                        $report[] = $this->changeOwnerMessage($lead, $newOwnerUserName);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:booked:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isSnooze()) {
                    try {
                        $this->leadStateService->snooze($lead, $newOwnerId, '', $creatorId, $reason);
                        $report[] = $this->changeOwnerMessage($lead, $newOwnerUserName);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:snooze:LeadId:' . $lead->id);
                    }
                }

            } else {
                \Yii::warning('Undefined action for multi update ', 'LeadMultiUpdateService:undefinedStatus:LeadId:' . $lead->id);
            }

        }
        return $report;
    }

    /**
     * @param int|null $newOwner
     * @param Lead $lead
     * @return int|null
     */
    private function getNewOwner(?int $newOwner, $lead): ?int
    {
        if ($newOwner === -1) {
            return null;
        }
        if ($newOwner === null) {
            return $lead->employee_id;
        }
        return $newOwner;
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
        return  '<span style="color: #28a048">Lead: ' . $lead->id . ' changed owner to ' . $newOwnerUserName . '</span>';
    }

    /**
     * @param int|null $id
     * @return string
     */
    private function getUserName(?int $id): string
    {
        if ($user = Employee::findOne($id)) {
            return $user->username;
        }
        return 'Undefined username';
    }

    private function redialProcess(LeadMultipleForm $form, Lead $lead): string
    {
        $report = '';
        if ($form->isRedialAdd()) {
            if ($this->qCallService->isExist($lead->id)) {
                $report = 'Lead: ' . $lead->id . ' already exist on Qcall List';
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
                        $report = 'Lead: ' . $lead->id . ' added to Qcall List';
                    } else {
                        $report = 'Lead: ' . $lead->id . ' not added to Qcall List';
                    }
                } catch (\Throwable $e) {
                    $report = 'Lead: ' . $lead->id . ' added to Qcall List error';
                }
            }
        } elseif ($form->isRedialRemove()) {
            if ($this->qCallService->isExist($lead->id)) {
                try {
                    $this->qCallService->remove($lead->id);
                    $report = 'Lead: ' . $lead->id . ' was removed from Qcall List';
                } catch (\Throwable $e) {
                    $report = 'Lead: ' . $lead->id . ' removed from Qcall List error';
                }
            } else {
                $report = 'Lead: ' . $lead->id . ' not found on Qcall List';
            }
        }
        return $report;
    }
}
