<?php

namespace modules\taskList\src\services\taskAssign\checker;

use common\models\Lead;
use common\models\query\ClientPhoneQuery;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\taskList\TaskListParamService;
use modules\taskList\src\helpers\TaskListHelper;

class SmsAssignChecker implements TaskAssignCheckerInterface
{
    private Lead $lead;
    private TaskList $taskList;

    public function __construct(
        Lead $lead,
        TaskList $taskList
    ) {
        $this->lead = $lead;
        $this->taskList = $taskList;
    }

    public function check(): bool
    {
        if (empty($this->lead->client_id)) {
            TaskListHelper::debug(
                'Client is empty. LeadID[' . $this->lead->id . ']',
                'info\UserTaskAssign:SmsAssignChecker:ClientEmpty'
            );
            return false;
        }

        if (!ClientPhoneQuery::getQueryClientPhoneByClientId($this->lead->client_id)->exists()) {
            TaskListHelper::debug(
                'Client Phone is empty. LeadID[' . $this->lead->id . ']',
                'info\UserTaskAssign:SmsAssignChecker:ClientPhoneIsEmpty'
            );
            return false;
        }

        if (!$this->lead->project->getParams()->sms->isEnabled()) {
            TaskListHelper::debug(
                'Project SMS not allowed. LeadID[' . $this->lead->id . ']',
                'info\UserTaskAssign:SmsAssignChecker:ProjectNotAllowed'
            );
            return false;
        }

        return true;
    }
}
