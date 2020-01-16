<?php

namespace sales\services\task;

use common\models\Lead;
use common\models\LeadTask;
use common\models\Task;

/**
 * Class LeadTaskService
 */
class LeadTaskService
{
    /**
     * @param Lead $lead
     */
    public function createLeadTasks(Lead $lead): void
    {
        if (!$lead->hasOwner()) {
            return;
        }

        $this->deleteUnnecessaryTasks($lead->id);

        if ($lead->isAnswered()) {
            $taskType = Task::CAT_ANSWERED_PROCESS;
        } else {
            $taskType = Task::CAT_NOT_ANSWERED_PROCESS;
        }

        LeadTask::createTaskList($lead->id, $lead->employee_id, 1, '', $taskType);
        LeadTask::createTaskList($lead->id, $lead->employee_id, 2, '', $taskType);
        LeadTask::createTaskList($lead->id, $lead->employee_id, 3, '', $taskType);
    }

    /**
     * @param int $leadId
     */
    private function deleteUnnecessaryTasks(int $leadId): void
    {
        LeadTask::deleteAll('lt_lead_id = :lead_id AND lt_date >= :date AND lt_completed_dt IS NULL',
            [':lead_id' => $leadId, ':date' => date('Y-m-d')]);
    }
}
