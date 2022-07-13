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
use src\helpers\app\AppHelper;
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

        if ($taskLists = TaskListQuery::getTaskListByLeadId($this->lead->id)) {
            if (!$userShiftSchedule = UserShiftScheduleQuery::getNextTimeLineByUser($this->lead->employee_id, $dtNow)) {
                throw new \RuntimeException('UserShiftSchedule not found by EmployeeId (' . $this->lead->employee_id . ')');
            }

            foreach ($taskLists as $taskList) {
                try {
                    $userTask = UserTask::create(
                        $this->lead->employee_id,
                        TargetObject::TARGET_OBJ_LEAD,
                        $this->lead->id,
                        $taskList->tl_id,
                        $dtNow->format('Y-m-d H:i:s'),
                        $userShiftSchedule->uss_end_utc_dt
                    );
                    if (!$userTask->validate()) {
                        throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($userTask, ' '));
                    }
                    (new UserTaskRepository($userTask))->save();

                    $shiftScheduleEventTask = ShiftScheduleEventTask::create(
                        $userShiftSchedule->uss_id,
                        $userTask->ut_id
                    );
                    if (!$shiftScheduleEventTask->validate()) {
                        throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($shiftScheduleEventTask, ' '));
                    }
                    (new ShiftScheduleEventTaskRepository($shiftScheduleEventTask))->save();
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

        /** @abac $leadTaskListAbacDto, LeadTaskListAbacObject::ASSIGN_TASK, LeadTaskListAbacObject::ACTION_ACCESS, Lead to task List assign checker */
        $can = Yii::$app->abac->can(
            new LeadTaskListAbacDto($this->lead, $this->lead->employee_id),
            LeadTaskListAbacObject::ASSIGN_TASK,
            LeadTaskListAbacObject::ACTION_ACCESS,
            $this->lead->employee ?? null
        );
        if (!$can) {
            if ($isResultBool) {
                return false;
            }
            throw new \RuntimeException('ABAC(' . LeadTaskListAbacObject::ASSIGN_TASK . ') is failed');
        }

        if ($this->hasActiveLeadObjectSegment()) {
            return true;
        }

        if ($isResultBool) {
            return false;
        }
        throw new \RuntimeException('Has ActiveLeadObjectSegment is false');
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }
}
