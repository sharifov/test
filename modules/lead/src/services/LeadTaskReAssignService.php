<?php

namespace modules\lead\src\services;

use common\models\Lead;
use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\userTask\repository\UserTaskRepository;
use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskQuery;
use src\helpers\ErrorsToStringHelper;

class LeadTaskReAssignService extends LeadTaskAssignService
{
    private Lead $lead;
    private TaskList $taskList;
    private \DateTimeImmutable $dtNow;
    private \DateTimeImmutable $dtNowWithDelay;
    private array $userShiftSchedules;
    private int $oldOwnerId;

    public function __construct(Lead $lead, TaskList $taskList, \DateTimeImmutable $dtNow, array $userShiftSchedules, int $oldOwnerId)
    {
        $this->lead = $lead;
        $this->taskList = $taskList;
        $this->dtNow = $dtNow;
        $this->dtNowWithDelay = $dtNow->modify(sprintf('+%d hour', $taskList->getDelayHoursParam()));
        $this->userShiftSchedules = $userShiftSchedules;
        $this->oldOwnerId = $oldOwnerId;
    }

    public function assign(): void
    {
        $existNewUserTaskComplete = UserTaskQuery::getQueryUserTaskByUserTaskListAndStatuses(
            $this->lead->employee_id,
            $this->taskList->tl_id,
            TargetObject::TARGET_OBJ_LEAD,
            $this->lead->id,
            [UserTask::STATUS_COMPLETE]
        )->exists();

        if ($existNewUserTaskComplete) {
            return;
        }

        $oldUserTask = UserTaskQuery::getQueryUserTaskByUserTaskList(
            $this->oldOwnerId,
            $this->taskList->tl_id,
            TargetObject::TARGET_OBJ_LEAD,
            $this->lead->id,
        )
            ->orderBy(['ut_status_id' => SORT_ASC])
            ->limit(1)->one();

        if (!$oldUserTask || $oldUserTask->isCanceled()) {
            (new LeadTaskFirstAssignService(
                $this->lead,
                $this->taskList,
                $this->dtNow,
                $this->userShiftSchedules
            ))->assign();
            return;
        }

        if ($oldUserTask->isComplete()) {
            return;
        }

        $userTask = $oldUserTask
            ->setOwner($this->lead->employee_id);

        if (!$userTask->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($userTask, ' '));
        }

        (new UserTaskRepository($userTask))->save();

        ShiftScheduleEventTask::deleteAll([
            'sset_user_task_id' => $userTask->ut_id
        ]);

        $this->createShiftScheduleEventTask($this->userShiftSchedules, $userTask, $this->dtNowWithDelay, $this->taskList->tl_duration_min);
    }
}
