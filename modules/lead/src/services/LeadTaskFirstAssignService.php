<?php

namespace modules\lead\src\services;

use common\models\Lead;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\userTask\repository\UserTaskRepository;
use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskQuery;
use src\helpers\ErrorsToStringHelper;

class LeadTaskFirstAssignService extends LeadTaskAssignService
{
    private Lead $lead;
    private TaskList $taskList;
    private \DateTimeImmutable $dtNow;
    private \DateTimeImmutable $dtNowWithDelay;
    private array $userShiftSchedules;

    public function __construct(Lead $lead, TaskList $taskList, \DateTimeImmutable $dtNow, array $userShiftSchedules)
    {
        $this->lead = $lead;
        $this->taskList = $taskList;
        $this->dtNow = $dtNow;
        $this->dtNowWithDelay = $dtNow->modify(sprintf('+%d hour', $taskList->getDelayHoursParam()));
        $this->userShiftSchedules = $userShiftSchedules;
    }

    public function assign(): void
    {
        $existNewUserTaskComplete = UserTaskQuery::getQueryUserTaskByUserTaskListAndStatuses(
            $this->lead->employee_id,
            $this->taskList->tl_id,
            TargetObject::TARGET_OBJ_LEAD,
            $this->lead->id,
            [UserTask::STATUS_PROCESSING, UserTask::STATUS_COMPLETE]
        )->exists();

        if ($existNewUserTaskComplete) {
            return;
        }

        $userTask = UserTask::create(
            $this->lead->employee_id,
            TargetObject::TARGET_OBJ_LEAD,
            $this->lead->id,
            $this->taskList->tl_id,
            $this->dtNow->format('Y-m-d H:i:s')
        );

        $userTask->setStatusProcessing();

        if (!$userTask->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($userTask, ' '));
        }

        (new UserTaskRepository($userTask))->save();

        $this->createShiftScheduleEventTask($this->userShiftSchedules, $userTask, $this->dtNowWithDelay, $this->taskList->tl_duration_min);
    }
}
