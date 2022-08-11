<?php

namespace frontend\widgets\multipleUpdate\lead;

use common\components\jobs\LeadPoorProcessingJob;
use common\models\Employee;
use common\models\Lead;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use src\auth\Auth;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\leadUserConversion\service\LeadUserConversionDictionary;
use src\model\leadUserConversion\service\LeadUserConversionService;
use src\services\lead\LeadStateService;
use src\services\lead\qcall\Config;
use src\services\lead\qcall\FindPhoneParams;
use src\services\lead\qcall\FindWeightParams;
use src\services\lead\qcall\QCallService;
use Yii;
use yii\bootstrap4\Html;

/**
 * Class LeadMultiUpdateService
 *
 * @property LeadStateService $leadStateService
 * @property QCallService $qCallService
 * @property LeadUserConversionService $leadUserConversionService
 * @property array $report
 */
class MultipleUpdateService
{
    private $leadStateService;
    private $qCallService;
    private $report;
    private LeadUserConversionService $leadUserConversionService;

    public function __construct(
        LeadStateService $leadStateService,
        QCallService $qCallService,
        LeadUserConversionService $leadUserConversionService
    ) {
        $this->leadStateService = $leadStateService;
        $this->qCallService = $qCallService;
        $this->report = [];
        $this->leadUserConversionService = $leadUserConversionService;
    }

    /**
     * @param MultipleUpdateForm $form
     * @return array
     */
    public function update(MultipleUpdateForm $form): array
    {
        $creatorId = $form->authUserId();

        foreach ($form->ids as $leadId) {
            if (!$lead = Lead::findOne($leadId)) {
                $this->addMessage('Not found Lead: ' . $leadId);
                continue;
            }

            //if (!$lead->isAvailableForMultiUpdate() && !$form->authUserIsAdmin()) {
            if (!Auth::can('leadSearchMultipleUpdate', ['lead' => $lead]) && !$form->authUserIsAdmin()) {
                $this->addMessage('Lead ID: ' . $leadId . ' with status "' . $lead->getStatusName() . '" is not available for Multiple Update (permission: leadSearchMultipleUpdate)'); //  Available only status: Processing, FollowUp, Hold, Trash, Snooze
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

            if ($form->needOwnerUpdate() && $oldOwnerId !== $form->userId && (!$form->needStatusUpdate() || $form->statusId === $lead->status)) {
                /** @abac $leadAbacDto, LeadAbacObject::ACT_CHANGE_OWNER, LeadAbacObject::ACTION_UPDATE, change of lead owner */
                if (!Yii::$app->abac->can(new LeadAbacDto($lead, null), LeadAbacObject::ACT_CHANGE_OWNER, LeadAbacObject::ACTION_UPDATE)) {
                    $this->addMessage('Lead ID: ' . $leadId . ' cannot be changed, because lead in sold status and has its owner');
                    continue;
                }
            }

            if ($form->needStatusUpdate()) {
                $this->changeStatus($lead, $form, $newOwner, $oldOwnerId, $creatorId);
            } elseif ($form->needOwnerUpdate()) {
                $this->changeOwner($lead, $form, $newOwner, $creatorId);
            } else {
                \Yii::warning('Undefined action for multi update ', 'lead\MultipleUpdateService:undefinedStatus:LeadId:' . $lead->id);
            }
        }

        return $this->report;
    }

    /**
     * @param Lead $lead
     * @param MultipleUpdateForm $form
     * @param NewOwner $newOwner
     * @param $creatorId
     */
    private function changeOwner(Lead $lead, MultipleUpdateForm $form, NewOwner $newOwner, $creatorId): void
    {
        if ($lead->isPending()) {
            try {
                $this->leadStateService->pending($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->changeOwnerMessage($lead, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($lead->isProcessing()) {
            try {
                $this->leadStateService->processing($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->changeOwnerMessage($lead, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($lead->isReject()) {
            try {
                $this->leadStateService->reject($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->changeOwnerMessage($lead, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($lead->isFollowUp()) {
            try {
                $this->leadStateService->followUp($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->changeOwnerMessage($lead, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($lead->isSold()) {
            try {
                $this->leadStateService->sold($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->changeOwnerMessage($lead, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($lead->isTrash()) {
            try {
                $this->leadStateService->trash($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->changeOwnerMessage($lead, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($lead->isBooked()) {
            try {
                $this->leadStateService->booked($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->changeOwnerMessage($lead, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($lead->isSnooze()) {
            try {
                $this->leadStateService->snooze($lead, $newOwner->id, '', $creatorId, $form->message);
                $this->addMessage($this->changeOwnerMessage($lead, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($lead->isNew()) {
            try {
                $this->leadStateService->new($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->changeOwnerMessage($lead, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * @param Lead $lead
     * @param MultipleUpdateForm $form
     * @param NewOwner $newOwner
     * @param $oldOwnerId
     * @param $creatorId
     */
    private function changeStatus(lead $lead, MultipleUpdateForm $form, NewOwner $newOwner, $oldOwnerId, $creatorId): void
    {
        if ($form->isPending()) {
            try {
                $this->leadStateService->pending($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->movedStateMessage($lead, 'Pending', $oldOwnerId, $newOwner->id, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($form->isProcessing()) {
            try {
                $ownerChanged = $oldOwnerId !== $newOwner->id;
                $oldStatusIsPending = $lead->isPending();
                $oldStatus = $lead->status;
                $this->leadStateService->processing($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->movedStateMessage($lead, 'Processing', $oldOwnerId, $newOwner->id, $newOwner->userName));
                if ($ownerChanged && $oldStatusIsPending) {
                    $this->leadUserConversionService->addAutomate(
                        $lead->id,
                        $newOwner->id,
                        LeadUserConversionDictionary::DESCRIPTION_ASSIGN,
                        $creatorId
                    );
                }

                if (
                    $oldStatus === Lead::STATUS_EXTRA_QUEUE &&
                    LeadPoorProcessingDataQuery::isExistActiveRule(LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_MULTIPLE_UPD)
                ) {
                    $description = $form->message ? 'Reason: ' . $form->message . '. ' : '';
                    if (($fromStatus = Lead::getStatus($oldStatus)) && $toStatus = Lead::getStatus(Lead::STATUS_PROCESSING)) {
                        $description .= sprintf(LeadPoorProcessingLogStatus::REASON_CHANGE_STATUS, $fromStatus, $toStatus);
                    }

                    LeadPoorProcessingService::addLeadPoorProcessingJob(
                        $lead->id,
                        [LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_MULTIPLE_UPD],
                        $description
                    );
                }
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($form->isReject()) {
            try {
                $this->leadStateService->reject($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->movedStateMessage($lead, 'Reject', $oldOwnerId, $newOwner->id, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($form->isFollowUp()) {
            try {
                $this->leadStateService->followUp($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->movedStateMessage($lead, 'Follow Up', $oldOwnerId, $newOwner->id, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($form->isSold()) {
            try {
                $this->leadStateService->sold($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->movedStateMessage($lead, 'Sold', $oldOwnerId, $newOwner->id, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($form->isTrash()) {
            try {
                $this->leadStateService->trash($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->movedStateMessage($lead, 'Trash', $oldOwnerId, $newOwner->id, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($form->isBooked()) {
            try {
                $this->leadStateService->booked($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->movedStateMessage($lead, 'Booked', $oldOwnerId, $newOwner->id, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($form->isSnooze()) {
            try {
                $this->leadStateService->snooze($lead, $newOwner->id, '', $creatorId, $form->message);
                $this->addMessage($this->movedStateMessage($lead, 'Snooze', $oldOwnerId, $newOwner->id, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($form->isNew()) {
            try {
                $this->leadStateService->new($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->movedStateMessage($lead, 'New', $oldOwnerId, $newOwner->id, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($form->isExtraQueue()) {
            try {
                $this->leadStateService->extraQueue($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->movedStateMessage($lead, 'Extra Queue', $oldOwnerId, $newOwner->id, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($form->isBusinessExtraQueue()) {
            try {
                $this->leadStateService->businessExtraQueue($lead, $newOwner->id, $creatorId, $form->message);
                $this->addMessage($this->movedStateMessage($lead, 'Extra Queue', $oldOwnerId, $newOwner->id, $newOwner->userName));
            } catch (\DomainException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } elseif ($form->isClosed()) {
            try {
                $this->leadStateService->close($lead, $form->reason, Auth::id(), $form->message);
                $this->addMessage($this->movedStateMessage($lead, 'Close Queue', $oldOwnerId, $newOwner->id, $newOwner->userName));
            } catch (\DomainException | \RuntimeException $e) {
                $this->addMessage('Lead: ' . $lead->id . ': ' . $e->getMessage());
            }
        } else {
            $this->addMessage('Undefined status: ' . $form->statusId . ' for multi update Lead: ' . $lead->id);
            \Yii::warning('Undefined status: ' . $form->statusId . ' for multi update Lead: ' . $lead->id, 'lead\MultipleUpdateService:changeStatus:undefinedStatus:LeadId:' . $lead->id);
        }
    }

    /**
     * @param array $reports
     * @return string
     */
    public function formatReport(array $reports): string
    {
        if (!$reports) {
            return '';
        }

        $out = '<ul>';
        foreach ($reports as $report) {
            $out .= Html::tag('li', Html::tag('span', $report, ['style' => 'color: #28a048']));
        }
        return $out . '</ul>';
    }

    /**
     * @param string $message
     */
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
            return '<span style="color: #28a048">Lead: ' . $lead->id . ' removed owner </span>';
        }
        return '<span style="color: #28a048">Lead: ' . $lead->id . ' changed owner to ' . $newOwnerUserName . '</span>';
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

    /**
     * @param MultipleUpdateForm $form
     * @param Lead $lead
     */
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
