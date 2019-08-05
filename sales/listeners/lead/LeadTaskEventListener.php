<?php

namespace sales\listeners\lead;

use common\models\LeadTask;
use common\models\Task;
use sales\events\lead\LeadTaskEvent;

/**
 * Class LeadTaskEventListener
 */
class LeadTaskEventListener
{

    /**
     * @param LeadTaskEvent $event
     */
    public function handle(LeadTaskEvent $event): void
    {

        $lead = $event->lead;

        if (!$lead->isGetOwner()) {
            return;
        }

        LeadTask::deleteUnnecessaryTasks($lead->id);

        if ($lead->l_answered) {
            $taskType = Task::CAT_ANSWERED_PROCESS;
        } else {
            $taskType = Task::CAT_NOT_ANSWERED_PROCESS;
        }

        LeadTask::createTaskList($lead->id, $lead->employee_id, 1, '', $taskType);
        LeadTask::createTaskList($lead->id, $lead->employee_id, 2, '', $taskType);
        LeadTask::createTaskList($lead->id, $lead->employee_id, 3, '', $taskType);

    }

}