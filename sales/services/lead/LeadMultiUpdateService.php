<?php

namespace sales\services\lead;

use common\models\Lead;
use common\models\Reason;
use frontend\models\LeadMultipleForm;

/**
 * Class LeadMultiUpdateService
 *
 * @property LeadStateService $leadStateService
 */
class LeadMultiUpdateService
{

    private $leadStateService;

    public function __construct(LeadStateService $leadStateService)
    {
        $this->leadStateService = $leadStateService;
    }

    public function update(LeadMultipleForm $multipleForm, int $creatorId): array
    {
        $report = [];
        foreach ($multipleForm->lead_list as $lead_id) {

            $leadId = (int)$lead_id;

            if (!$lead = Lead::findOne($leadId)) {
                $report[] = 'Not found Lead: ' . $leadId;
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

            $newOwnerId = $this->getNewOwner($multipleForm->employee_id, $lead);

            if ($multipleForm->status_id) {

                if ($multipleForm->status_id == Lead::STATUS_PENDING) {
                    try {
                        $this->leadStateService->pending($lead, $newOwnerId, $creatorId, $reason);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:pending:LeadId:' . $lead->id);
                    }
                } elseif ($multipleForm->status_id == Lead::STATUS_PROCESSING) {
                    try {
                        $this->leadStateService->processing($lead, $newOwnerId, $creatorId, $reason);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:processing:LeadId:' . $lead->id);
                    }
                } elseif ($multipleForm->status_id == Lead::STATUS_REJECT) {
                    try {
                        $this->leadStateService->reject($lead, $newOwnerId, $creatorId, $reason);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:reject:LeadId:' . $lead->id);
                    }
                } elseif ($multipleForm->status_id == Lead::STATUS_FOLLOW_UP) {
                    try {
                        $this->leadStateService->followUp($lead, $newOwnerId, $creatorId, $reason);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:followUp:LeadId:' . $lead->id);
                    }
                } elseif ($multipleForm->status_id == Lead::STATUS_SOLD) {
                    try {
                        $this->leadStateService->sold($lead, $newOwnerId, $creatorId, $reason);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:sold:LeadId:' . $lead->id);
                    }
                } elseif ($multipleForm->status_id == Lead::STATUS_TRASH) {
                    try {
                        $this->leadStateService->trash($lead, $newOwnerId, $creatorId, $reason);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:trash:LeadId:' . $lead->id);
                    }
                } elseif ($multipleForm->status_id == Lead::STATUS_BOOKED) {
                    try {
                        $this->leadStateService->booked($lead, $newOwnerId, $creatorId, $reason);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:booked:LeadId:' . $lead->id);
                    }
                } elseif ($multipleForm->status_id == Lead::STATUS_SNOOZE) {
                    try {
                        $this->leadStateService->snooze($lead, $newOwnerId, '', $creatorId, $reason);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:snooze:LeadId:' . $lead->id);
                    }
                } else {
                    \Yii::warning('Undefined status: '. $multipleForm->status_id . ' for multi update Lead: ' . $lead->id, 'LeadMultiUpdateService:undefinedStatus:LeadId:' . $lead->id);
                }

            } elseif ($multipleForm->employee_id) {

                if ($lead->isPending()) {
                    try {
                        $this->leadStateService->pending($lead, $newOwnerId, $creatorId, $reason);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:pending:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isProcessing()) {
                    try {
                        $this->leadStateService->processing($lead, $newOwnerId, $creatorId, $reason);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:processing:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isReject()) {
                    try {
                        $this->leadStateService->reject($lead, $newOwnerId, $creatorId, $reason);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:reject:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isFollowUp()) {
                    try {
                        $this->leadStateService->followUp($lead, $newOwnerId, $creatorId, $reason);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:followUp:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isSold()) {
                    try {
                        $this->leadStateService->sold($lead, $newOwnerId, $creatorId, $reason);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:sold:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isTrash()) {
                    try {
                        $this->leadStateService->trash($lead, $newOwnerId, $creatorId, $reason);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:trash:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isBooked()) {
                    try {
                        $this->leadStateService->booked($lead, $newOwnerId, $creatorId, $reason);
                    } catch (\DomainException $e) {
                        $report[] = 'Lead: ' . $lead->id . ': ' . $e->getMessage();
                        \Yii::error($e->getMessage(), 'LeadMultiUpdateService:booked:LeadId:' . $lead->id);
                    }
                } elseif ($lead->isSnooze()) {
                    try {
                        $this->leadStateService->snooze($lead, $newOwnerId, '', $creatorId, $reason);
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
     * @param $newOwner
     * @param Lead $lead
     * @return int|null
     */
    private function getNewOwner($newOwner, $lead): ?int
    {
        if ($newOwner == -1) {
            return null;
        }
        if ($newOwner === null) {
            return $lead->employee_id;
        }
        return (int)$newOwner;
    }
}
