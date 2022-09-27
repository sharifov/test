<?php

namespace modules\lead\src\services;

use common\models\Lead;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\userTask\repository\UserTaskRepository;
use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskQuery;
use modules\taskList\src\entities\taskList\TaskListParamService;
use src\helpers\ErrorsToStringHelper;

class LeadTaskFirstAssignService extends LeadTaskAssignService
{
    private Lead $lead;
    private TaskList $taskList;
    private \DateTimeImmutable $dtStartWithDelay;
    private array $userShiftSchedules;

    public function __construct(Lead $lead, TaskList $taskList, \DateTimeImmutable $dtNow, array $userShiftSchedules)
    {
        $this->lead = $lead;
        $this->taskList = $taskList;
        $this->dtStartWithDelay = $dtNow->modify(sprintf('+%d hour', (new TaskListParamService($taskList))->getDelayHoursParam()));
        $this->userShiftSchedules = $userShiftSchedules;
    }

    /**
     * @return int|null
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function assign(): ?int
    {
        $existNewUserTaskComplete = UserTaskQuery::getQueryUserTaskByUserTaskListAndStatuses(
            $this->lead->employee_id,
            $this->taskList->tl_id,
            TargetObject::TARGET_OBJ_LEAD,
            $this->lead->id,
            [UserTask::STATUS_PROCESSING, UserTask::STATUS_COMPLETE]
        )->exists();

        if ($existNewUserTaskComplete) {
            \modules\taskList\src\helpers\TaskListHelper::debug(
                'Exist UserTask Complete or Processing (Lead ID: ' . $this->lead->id . ', EmployeeID: ' . $this->lead->employee_id . '), TaskLIst ID (' . $this->taskList->tl_id . ')',
                'info\UserTaskAssign:LeadTaskReAssignService:assign:info'
            );

            return null;
        }

        $taskListEndDt = null;
        if ((int) $this->taskList->tl_duration_min > 0) {
            $taskListEndDt = $this->dtStartWithDelay->modify(sprintf('+%d minutes', (int) $this->taskList->tl_duration_min));
        }

        $userTask = UserTask::create(
            $this->lead->employee_id,
            TargetObject::TARGET_OBJ_LEAD,
            $this->lead->id,
            $this->taskList->tl_id,
            $this->dtStartWithDelay->format('Y-m-d H:i:s'),
            $taskListEndDt ? $taskListEndDt->format('Y-m-d H:i:s') : null
        );

        $userTask->setStatusProcessing();

        if (!$userTask->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($userTask, ' '));
        }

        (new UserTaskRepository($userTask))->save();

        $this->createShiftScheduleEventTask($this->userShiftSchedules, $userTask, $this->dtStartWithDelay, $this->taskList->tl_duration_min);

        return $userTask->ut_id;
    }
}
