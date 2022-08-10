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
        $oldUserTask = UserTaskQuery::getQueryUserTaskByTargetObjectAndTaskList(
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
            \modules\taskList\src\helpers\TaskListHelper::debug(
                'Exist OldUserTask Complete (Lead ID: ' . $this->lead->id . ', EmployeeID: ' . $this->oldOwnerId . '), TaskLIst ID (' . $this->taskList->tl_id . ')',
                'info\UserTaskAssign:LeadTaskReAssignService:assign:info'
            );
            return;
        }

        $userTask = $oldUserTask
            ->setOwner($this->lead->employee_id)
            ->setStartDate($this->dtNowWithDelay->format('Y-m-d H:i:s'));

        if ((int) $this->taskList->tl_duration_min > 0) {
            $taskListEndDt = $this->dtNowWithDelay->modify(sprintf('+%d minutes', (int) $this->taskList->tl_duration_min));
            $userTask->setEndDate($taskListEndDt->format('Y-m-d H:i:s'));
        }

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
