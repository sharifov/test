<?php

namespace modules\lead\src\services;

use common\models\Lead;
use modules\featureFlag\FFlag;
use modules\lead\src\abac\taskLIst\LeadTaskListAbacDto;
use modules\lead\src\abac\taskLIst\LeadTaskListAbacObject;
use modules\objectSegment\src\contracts\ObjectSegmentKeyContract;
use modules\objectSegment\src\entities\ObjectSegmentList;
use modules\objectSegment\src\entities\ObjectSegmentTask;
use modules\objectSegment\src\entities\ObjectSegmentType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\taskList\src\entities\shiftScheduleEventTask\repository\ShiftScheduleEventTaskRepository;
use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\taskList\TaskListQuery;
use modules\taskList\src\entities\userTask\repository\UserTaskRepository;
use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\exceptions\TaskListAssignException;
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
    private bool $isNewOwner;

    public function __construct(Lead $lead, bool $isNewOwner = true)
    {
        $this->lead = $lead;
        $this->isNewOwner = $isNewOwner;
    }

    public function assign(): void
    {
        $dtNow = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
        try {
            if ($taskLists = TaskListQuery::getTaskListByLeadId($this->lead->id)) {
                foreach ($taskLists as $taskList) {
                    try {
                        $dtNowWithDelay = $dtNow->modify(sprintf('+%d hour', $taskList->getDelayHoursParam()));
                        $userShiftSchedules = UserShiftScheduleQuery::getAllFromStartDateByUserId($this->lead->employee_id, $dtNowWithDelay);
                        $duration = $taskList->tl_duration_min;
                        $userTaskListEndDate = null;

                        if ($userShiftSchedules === null) {
                            throw new TaskListAssignException('UserShiftSchedules not found by EmployeeId (' . $this->lead->employee_id . ') and StartDateTime:' . $dtNowWithDelay->format('Y-m-d H:i:s'));
                        }

                        $userTask = UserTask::create(
                            $this->lead->employee_id,
                            TargetObject::TARGET_OBJ_LEAD,
                            $this->lead->id,
                            $taskList->tl_id,
                            $dtNow->format('Y-m-d H:i:s')
                        );

                        $userTask->setStatusProcessing();

                        if (!$userTask->validate()) {
                            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($userTask, ' '));
                        }

                        (new UserTaskRepository($userTask))->save();

                        foreach ($userShiftSchedules as $userShiftSchedule) {
                            $shiftScheduleEventTask = ShiftScheduleEventTask::create(
                                $userShiftSchedule->uss_id,
                                $userTask->ut_id
                            );

                            if (!$shiftScheduleEventTask->validate()) {
                                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($shiftScheduleEventTask, ' '));
                            }

                            (new ShiftScheduleEventTaskRepository($shiftScheduleEventTask))->save();

                            if ($taskList->tl_duration_min !== null) {
                                if (DateHelper::toFormatByUTC($userShiftSchedule->uss_start_utc_dt) === $dtNowWithDelay->format('Y-m-d')) {
                                    $leftMinutes = DateHelper::getDifferentInMinutesByDatesUTC($dtNowWithDelay->format('Y-m-d H:i:s'), $userShiftSchedule->uss_end_utc_dt);
                                    $calculatedDuration = $duration - $leftMinutes;
                                } else {
                                    $calculatedDuration = $duration - $userShiftSchedule->uss_duration;
                                }

                                if ($calculatedDuration <= 0) {
                                    $userTaskListEndDate = DateHelper::getDateTimeWithAddedMinutesUTC($userShiftSchedule->uss_end_utc_dt, $duration);
                                    break;
                                }

                                $duration = $calculatedDuration;
                            }
                        }

                        if ($userTaskListEndDate !== null) {
                            $userTask->ut_end_dt = $userTaskListEndDate;

                            if (!$userTask->validate()) {
                                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($userTask, ' '));
                            }

                            (new UserTaskRepository($userTask))->save();
                        }
                    } catch (TaskListAssignException $exception) {
                        $message = AppHelper::throwableLog($exception);
                        \Yii::info($message, 'info\LeadTaskListService:assignReAssign:TaskListAssignException');
                    } catch (\RuntimeException | \DomainException $throwable) {
                        $message = AppHelper::throwableLog($throwable);
                        if (isset($userTask)) {
                            $message['userTask'] = ArrayHelper::toArray($userTask);
                        }
                        \Yii::warning($message, 'LeadTaskListService:assignReAssign:Exception');
                    } catch (\Throwable $throwable) {
                        $message = AppHelper::throwableLog($throwable);
                        if (isset($userTask)) {
                            $message['userTask'] = ArrayHelper::toArray($userTask);
                        }
                        \Yii::error($message, 'LeadTaskListService:assignReAssign:Throwable');
                    }
                }
                return;
            }
            throw new TaskListAssignException('TaskList not found by LeadId (' . $this->lead->id . ')');
        } catch (TaskListAssignException $exception) {
            $message = AppHelper::throwableLog($exception);
            \Yii::info($message, 'info\LeadTaskListService:assignReAssign:TaskListAssignException');
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
        /** @fflag FFlag::FF_KEY_LEAD_TASK_ASSIGN, Lead to task List assign checker */
        if (!Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_LEAD_TASK_ASSIGN)) {
            if ($isResultBool) {
                return false;
            }
            throw new \RuntimeException('Feature Flag(' . FFlag::FF_KEY_LEAD_TASK_ASSIGN . ') is disabled');
        }

        if (!$employee = $this->lead->employee ?? null) {
            if ($isResultBool) {
                return false;
            }
            throw new \RuntimeException('Lead owner is empty');
        }

        /** @abac $leadTaskListAbacDto, LeadTaskListAbacObject::PROCESSING_TASK, LeadTaskListAbacObject::ACTION_ACCESS, Lead to task List processing checker */
        $can = Yii::$app->abac->can(
            new LeadTaskListAbacDto($this->lead, $this->lead->employee_id),
            LeadTaskListAbacObject::PROCESSING_TASK,
            LeadTaskListAbacObject::ACTION_ACCESS,
            $employee
        );
        if (!$can) {
            if ($isResultBool) {
                return false;
            }
            throw new \RuntimeException('ABAC(' . LeadTaskListAbacObject::PROCESSING_TASK . ') is failed');
        }

        if (!$this->hasActiveLeadObjectSegment()) {
            if ($isResultBool) {
                return false;
            }
            throw new \RuntimeException('Has ActiveLeadObjectSegment is false');
        }

        return true;
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }

    public function isNewOwner(): bool
    {
        return $this->isNewOwner;
    }
}
