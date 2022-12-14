<?php

namespace modules\lead\src\services;

use common\models\Lead;
use common\models\LeadTask;
use frontend\helpers\RedisHelper;
use modules\featureFlag\FFlag;
use modules\lead\src\abac\taskLIst\LeadTaskListAbacDto;
use modules\lead\src\abac\taskLIst\LeadTaskListAbacObject;
use modules\objectSegment\src\contracts\ObjectSegmentKeyContract;
use modules\objectSegment\src\entities\ObjectSegmentList;
use modules\objectSegment\src\entities\ObjectSegmentTask;
use modules\objectSegment\src\entities\ObjectSegmentType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\taskList\src\entities\shiftScheduleEventTask\repository\ShiftScheduleEventTaskRepository;
use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\taskList\TaskListQuery;
use modules\taskList\src\entities\userTask\repository\UserTaskRepository;
use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskQuery;
use modules\taskList\src\exceptions\TaskListAssignException;
use modules\taskList\src\notifications\Task\LeadTasksListSavedNotification;
use modules\taskList\src\services\taskAssign\checker\TaskListAssignCheckerFactory;
use modules\taskList\src\entities\taskList\TaskListParamService;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\helpers\DateHelper;
use src\helpers\ErrorsToStringHelper;
use src\model\leadData\entity\LeadData;
use src\model\leadData\services\LeadDataService;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Class LeadTaskListService
 */
class LeadTaskListService
{
    private Lead $lead;
    public ?int $oldOwnerId;

    public function __construct(Lead $lead, ?int $oldOwnerId = null)
    {
        $this->lead = $lead;
        $this->oldOwnerId = $oldOwnerId;
    }

    public function assign(): void
    {
        try {
            $dtNow = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')));

            if (!$this->isDuplicateAssignTasks() && $taskLists = TaskListQuery::getTaskListByLeadId($this->lead->id)) {
                $idsOfSuccessAddedUserTasks = [];

                foreach ($taskLists as $taskList) {
                    try {
                        $assignChecker = (new TaskListAssignCheckerFactory(
                            $taskList,
                            $this->lead
                        ))->create();

                        if (!$assignChecker->check()) {
                            \modules\taskList\src\helpers\TaskListHelper::debug(
                                'Not Found Valid Phone or Email (Lead ID: ' . $this->lead->id . ', Task Object: ' . $taskList->tl_object . ')',
                                'info\UserTaskAssign:LeadTaskListService:assignReAssign:info'
                            );
                            continue;
                        }

                        $taskListParams = new TaskListParamService($taskList);
                        $dtStart = $this->getStartDate($dtNow, $taskList, $taskListParams);

                        $dtWithDelay = $dtStart->modify(sprintf('+%d hour', $taskListParams->getDelayHoursParam()));

                        $taskListEndDt = null;
                        if ((int)$taskList->tl_duration_min > 0) {
                            $taskListEndDt = $dtWithDelay->modify(sprintf('+%d minutes', $taskList->tl_duration_min));
                        }

                        $userShiftSchedules = UserShiftScheduleQuery::getAllFromStartDateByUserId(
                            $this->lead->employee_id,
                            $dtWithDelay,
                            $taskListEndDt
                        );

                        if (empty($userShiftSchedules)) {
                            $this->canceledUserTask($taskList->tl_id);
                            throw new TaskListAssignException('UserShiftSchedules not found by EmployeeId (' .
                                $this->lead->employee_id . ') and StartDateTime:' . $dtWithDelay->format('Y-m-d H:i:s')
                                . ' and EndTime:' . ($taskListEndDt ? $taskListEndDt->format('Y-m-d H:i:s') : 'null'));
                        }

                        if ($this->isEmptyOldOwner()) {
                            $assignService = new LeadTaskFirstAssignService(
                                $this->lead,
                                $taskList,
                                $dtStart,
                                $userShiftSchedules
                            );
                        } else {
                            $assignService = new LeadTaskReAssignService(
                                $this->lead,
                                $taskList,
                                $dtStart,
                                $userShiftSchedules,
                                $this->oldOwnerId
                            );
                        }

                        $userTaskId = $assignService->assign();

                        if (!empty($userTaskId)) {
                            $idsOfSuccessAddedUserTasks[] = $userTaskId;
                        }
                    } catch (TaskListAssignException $exception) {
                        $message = AppHelper::throwableLog($exception);
                        $message['taskListId'] = $taskList->tl_id ?? null;
                        $message['leadId'] = $this->lead->id ?? null;
                        \Yii::info(
                            $message,
                            'info\LeadTaskListService:assignReAssign:TaskListAssignException'
                        );
                    } catch (\RuntimeException | \DomainException $throwable) {
                        $message = AppHelper::throwableLog($throwable);
                        $message['taskListId'] = $taskList->tl_id ?? null;
                        $message['leadId'] = $this->lead->id ?? null;
                        \Yii::warning($message, 'LeadTaskListService:assignReAssign:Exception');
                    } catch (\Throwable $throwable) {
                        $message = AppHelper::throwableLog($throwable);
                        $message['taskListId'] = $taskList->tl_id ?? null;
                        $message['leadId'] = $this->lead->id ?? null;
                        \Yii::error($message, 'LeadTaskListService:assignReAssign:Throwable');
                    }
                }

                if (!empty($idsOfSuccessAddedUserTasks)) {
                    (new LeadTasksListSavedNotification($this->lead))->send();
                }
                return;
            }
            throw new TaskListAssignException('TaskList not found by LeadId (' . $this->lead->id . ')');
        } catch (TaskListAssignException $exception) {
            \Yii::info(
                AppHelper::throwableLog($exception),
                'info\LeadTaskListService:assign:TaskListAssignException'
            );
        } catch (\Exception $exception) {
            \Yii::info(
                AppHelper::throwableLog($exception),
                'info\LeadTaskListService:assign:Exception'
            );
        }
    }

    public function hasActiveLeadObjectSegment(): bool
    {
        return LeadData::find()
            ->innerJoin([
                'object_segment_list_query' => ObjectSegmentList::find()
                    ->select(['osl_key'])
                    ->innerJoin(
                        ObjectSegmentType::tableName(),
                        'osl_ost_id = ost_id AND ost_key = :keyLead',
                        ['keyLead' => ObjectSegmentKeyContract::TYPE_KEY_LEAD]
                    )
                    ->where(['osl_enabled' => true])
                    ->groupBy(['osl_key'])
            ], 'object_segment_list_query.osl_key = ld_field_value')
            ->where(['ld_lead_id' => $this->lead->id])
            ->andWhere(['ld_field_key' => LeadDataKeyDictionary::KEY_LEAD_OBJECT_SEGMENT])
            ->exists();
    }

    public function isProcessAllowed(bool $isResultBool = true): bool
    {
        if (!$this->isEnableFFAndNotEmptyOwner($isResultBool)) {
            return false;
        }

        /** @abac $leadTaskListAbacDto, LeadTaskListAbacObject::PROCESSING_TASK, LeadTaskListAbacObject::ACTION_ACCESS, Lead to task List processing checker */
        $can = Yii::$app->abac->can(
            new LeadTaskListAbacDto($this->lead, $this->lead->employee_id),
            LeadTaskListAbacObject::PROCESSING_TASK,
            LeadTaskListAbacObject::ACTION_ACCESS,
            $this->lead->employee
        );
        if (!$can) {
            if ($isResultBool) {
                \modules\taskList\src\helpers\TaskListHelper::debug(
                    'ABAC(' . LeadTaskListAbacObject::PROCESSING_TASK . ') is failed (Lead ID: ' . $this->lead->id . ')',
                    'info\UserTaskAssign:LeadTaskListService:isProcessAllowed:info'
                );
                return false;
            }
            throw new \RuntimeException('ABAC(' . LeadTaskListAbacObject::PROCESSING_TASK . ') is failed');
        }

        if (!$this->hasActiveLeadObjectSegment()) {
            if ($isResultBool) {
                \modules\taskList\src\helpers\TaskListHelper::debug(
                    'Has ActiveLeadObjectSegment is false (Lead ID: ' . $this->lead->id . ')',
                    'info\UserTaskAssign:LeadTaskListService:isProcessAllowed:info'
                );
                return false;
            }
            throw new \RuntimeException('Has ActiveLeadObjectSegment is false');
        }

        return true;
    }

    public function isEnableFFAndNotEmptyOwner(bool $isResultBool = true): bool
    {
        /** @fflag FFlag::FF_KEY_LEAD_TASK_ASSIGN, Lead to task List assign checker */
        if (!Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_LEAD_TASK_ASSIGN)) {
            if ($isResultBool) {
                \modules\taskList\src\helpers\TaskListHelper::debug(
                    'Feature Flag(' . FFlag::FF_KEY_LEAD_TASK_ASSIGN . ') is disabled',
                    'info\UserTaskAssign:LeadTaskListService:isEnableFFAndNotEmptyOwner:info'
                );
                return false;
            }
            throw new \RuntimeException('Feature Flag(' . FFlag::FF_KEY_LEAD_TASK_ASSIGN . ') is disabled');
        }

        if (!$this->lead->employee ?? null) {
            if ($isResultBool) {
                \modules\taskList\src\helpers\TaskListHelper::debug(
                    'Lead(ID:' . $this->lead->id . ') owner is empty',
                    'info\UserTaskAssign:LeadTaskListService:isEnableFFAndNotEmptyOwner:info'
                );
                return false;
            }
            throw new \RuntimeException('Lead owner is empty');
        }
        return true;
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }

    public function isEmptyOldOwner(): bool
    {
        return empty($this->oldOwnerId);
    }

    private function canceledUserTask(int $taskListId)
    {
        if (!$this->isEmptyOldOwner()) {
            $userTask = UserTaskQuery::getQueryUserTaskByUserTaskListAndStatuses(
                $this->oldOwnerId,
                $taskListId,
                TargetObject::TARGET_OBJ_LEAD,
                $this->lead->id,
                [UserTask::STATUS_PROCESSING]
            )->limit(1)->one();

            if ($userTask) {
                $userTask->setStatusCancel();
                (new UserTaskRepository($userTask))->save();
            }
        }
    }

    public function canceledAllUserTask()
    {
        $userTasks = UserTaskQuery::getQueryUserTaskByTargetIdAndStatuses(
            $this->lead->id,
            [UserTask::STATUS_PROCESSING]
        )->all();

        foreach ($userTasks as $userTask) {
            $userTask->setStatusCancel();
            (new UserTaskRepository($userTask))->save();
        }
    }

    private function getStartDate(\DateTimeImmutable $dtStart, TaskList $taskList, TaskListParamService $taskListParams): \DateTimeImmutable
    {
        if ($taskListParams->getDelayHoursParam() === 0) {
            $firstShiftScheduleStartDate = UserShiftScheduleQuery::getQueryForNextShiftsByUserId(
                $this->lead->employee_id,
                $dtStart
            )
                ->select('user_shift_schedule.uss_start_utc_dt')
                ->offset($taskListParams->getDelayShiftParam())
                ->limit(1)
                ->scalar();

            if (empty($firstShiftScheduleStartDate)) {
                $this->canceledUserTask($taskList->tl_id);
                throw new TaskListAssignException('UserShiftSchedules not found by EmployeeId (' .
                    $this->lead->employee_id . ') and StartDateTime:' . $dtStart->format('Y-m-d H:i:s') . ' and ShiftDelay:' . $taskListParams->getDelayShiftParam());
            }

            if (strtotime($firstShiftScheduleStartDate) > time()) {
                $dtStart = (new \DateTimeImmutable($firstShiftScheduleStartDate, new \DateTimeZone('UTC')));
            }
        }
        return $dtStart;
    }

    public static function isDuplicateByUserId()
    {
        $key = 'LeadTaskListService_handleByUserId_' . Auth::id();
        return RedisHelper::checkDuplicate($key, 2);
    }

    /**
     * @return bool
     */
    private function isDuplicateAssignTasks(): bool
    {
        $key = 'LeadTaskListService_handle_' . $this->lead->id;
        return RedisHelper::checkDuplicate($key, 2);
    }
}
