<?php

namespace modules\taskList\src\services;

use common\models\Call;
use src\model\callLog\entity\callLog\CallLog;
use src\model\callLog\entity\callLog\CallLogStatus;
use src\model\callLog\entity\callLog\CallLogType;
use src\model\callLog\entity\callLogLead\CallLogLead;

class TargetObjectCallService
{
    private Call $call;
    private ?string $userTaskStartDT;
    private ?string $userTaskEndDT;

    private int $callCompletedDuration = 30;

    private int $targetObjectCallAttempts = 0;
    private int $targetObjectCallCompleted = 0;

    private array $leadCallAttemptsStatuses = [
        CallLogStatus::NOT_ANSWERED,
        CallLogStatus::BUSY,
        CallLogStatus::FAILED,
        CallLogStatus::DECLINED,
        CallLogStatus::CANCELED,
    ];

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
        $query = CallLogLead::find()
            ->innerJoin(
                CallLog::tableName(),
                CallLog::tableName() . '.cl_id = ' . CallLogLead::tableName() . '.cll_cl_id'
            )
            ->where(['cll_lead_id' => $leadId])
            ->andWhere(['cl_type_id' =>  CallLogType::OUT])
            ->andWhere(['cl_status_id' => CallLogStatus::COMPLETE])
            ->andWhere(['>=', 'cl_duration', $this->getCallCompletedDuration()])
        ;

        if ($this->userTaskStartDT) {
            $query->andWhere(['>=', 'cl_call_created_dt', $this->userTaskStartDT]);
        }
        if ($this->userTaskEndDT) {
            $query->andWhere(['<=', 'cl_call_created_dt', $this->userTaskEndDT]);
        }

        return (int) $query->count();
    }

    private function countLeadCallAttempts(int $leadId): int
    {
        $query = CallLogLead::find()
            ->innerJoin(
                CallLog::tableName(),
                CallLog::tableName() . '.cl_id = ' . CallLogLead::tableName() . '.cll_cl_id'
            )
            ->where(['cll_lead_id' => $leadId])
            ->andWhere(['cl_type_id' =>  CallLogType::OUT])
            ->andWhere(['IN', 'cl_status_id' , $this->getLeadCallAttemptsStatuses()])
        ;

        if ($this->userTaskStartDT) {
            $query->andWhere(['>=', 'cl_call_created_dt', $this->userTaskStartDT]);
        }
        if ($this->userTaskEndDT) {
            $query->andWhere(['<=', 'cl_call_created_dt', $this->userTaskEndDT]);
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

    public function getLeadCallAttemptsStatuses(): array
    {
        return $this->leadCallAttemptsStatuses;
    }

    public function setLeadCallAttemptsStatuses(array $leadCallAttemptsStatuses): void
    {
        $this->leadCallAttemptsStatuses = $leadCallAttemptsStatuses;
    }
}
