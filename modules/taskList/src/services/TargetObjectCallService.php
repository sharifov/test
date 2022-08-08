<?php

namespace modules\taskList\src\services;

use common\models\Call;

class TargetObjectCallService
{
    private Call $call;
    private ?string $userTaskStartDT;
    private ?string $userTaskEndDT;

    private int $callCompletedDuration = 30;

    private int $targetObjectCallAttempts = 0;
    private int $targetObjectCallCompleted = 0;

    public function __construct(Call $call, ?string $userTaskStartDT = null, ?string $userTaskEndDT = null)
    {
        $this->call = $call;
        $this->userTaskStartDT = $userTaskStartDT;
        $this->userTaskEndDT = $userTaskEndDT;
    }

    public function handleCall(): TargetObjectCallService
    {
        if ($leadId = $this->call->c_lead_id) {
            $this->targetObjectCallCompleted += $this->countLeadCallProcessing($leadId);
            $this->targetObjectCallAttempts += $this->countLeadCallAttempts($leadId);
        }
        /* TODO:: add case counting */

        return $this;
    }

    private function countLeadCallProcessing(int $leadId): int
    {
        $query = Call::find()
            ->where(['c_lead_id' => $leadId])
            ->andWhere(['c_call_type_id' => Call::CALL_TYPE_OUT])
            ->andWhere(['c_status_id' => Call::STATUS_COMPLETED])
            ->andWhere(['>=', 'c_call_duration', $this->getCallCompletedDuration()])
        ;

        if ($this->userTaskStartDT) {
            $query->andWhere(['>=', 'c_created_dt', $this->userTaskStartDT]);
        }
        if ($this->userTaskEndDT) {
            $query->andWhere(['<=', 'c_created_dt', $this->userTaskEndDT]);
        }

        return (int) $query->count();
    }

    private function countLeadCallAttempts(int $leadId): int
    {
        $query = Call::find()
            ->where(['c_lead_id' => $leadId])
            ->andWhere(['c_call_type_id' => Call::CALL_TYPE_OUT])
            ->andWhere(['c_status_id' => Call::STATUS_NO_ANSWER])
        ;

        if ($this->userTaskStartDT) {
            $query->andWhere(['>=', 'c_created_dt', $this->userTaskStartDT]);
        }
        if ($this->userTaskEndDT) {
            $query->andWhere(['<=', 'c_created_dt', $this->userTaskEndDT]);
        }

        return (int) $query->count();
    }

    public function getTargetObjectCallAttempts(): int
    {
        return $this->targetObjectCallAttempts;
    }

    public function getTargetObjectCallCompleted(): int
    {
        return $this->targetObjectCallCompleted;
    }

    public function setCallCompletedDuration(int $callCompletedDuration): TargetObjectCallService
    {
        $this->callCompletedDuration = $callCompletedDuration;
        return $this;
    }

    public function getCallCompletedDuration(): int
    {
        return $this->callCompletedDuration;
    }
}
