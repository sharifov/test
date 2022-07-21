<?php

namespace modules\taskList\src\entities\taskList;

use modules\objectSegment\src\contracts\ObjectSegmentKeyContract;
use modules\objectSegment\src\entities\ObjectSegmentList;
use modules\objectSegment\src\entities\ObjectSegmentTask;
use modules\objectSegment\src\entities\ObjectSegmentType;
use modules\taskList\src\entities\userTask\UserTask;
use src\model\leadData\entity\LeadData;

/**
 * Class TaskListQuery
 */
class TaskListQuery
{
    /**
     * @return TaskList[]
     */
    public static function getTaskListByLeadId(int $leadId, bool $enableType = true): array
    {
        return TaskList::find()
            ->alias('task_list')
            ->select('task_list.*')
            ->innerJoin([
                'object_segment_task_query' => ObjectSegmentTask::find()
                    ->select(['ostl_tl_id'])
                    ->innerJoin([
                        'object_segment_list_query' => ObjectSegmentList::find()
                            ->select(['osl_id'])
                            ->innerJoin(
                                ObjectSegmentType::tableName(),
                                'osl_ost_id = ost_id AND ost_key = :keyLead',
                                ['keyLead' => ObjectSegmentKeyContract::TYPE_KEY_LEAD]
                            )
                            ->innerJoin([
                                'lead_data_query' => LeadData::find()
                                    ->select(['ld_field_value'])
                                    ->andWhere(['ld_lead_id' => $leadId])
                                    ->groupBy(['ld_field_value'])
                            ], 'lead_data_query.ld_field_value = object_segment_list.osl_key')
                            ->andWhere(['osl_enabled' => true])
                            ->distinct()
                    ], 'osl_id = ostl_osl_id')
                    ->groupBy(['ostl_tl_id'])
            ], 'object_segment_task_query.ostl_tl_id = task_list.tl_id')
            ->where(['tl_enable_type' => $enableType])
            ->distinct()
            ->all();
    }

    public static function getTaskListUserCompletion(
        int $userId,
        string $targetObject,
        int $targetObjectId,
        string $taskObject,
        array $utStatusIds
    ): Scopes {
        return TaskList::find()
            ->alias('task_list')
            ->innerJoin([
                'user_task_query' => UserTask::find()
                    ->select(['ut_task_list_id'])
                    ->where(['ut_user_id' => $userId])
                    ->andWhere(['ut_target_object' => $targetObject])
                    ->andWhere(['ut_target_object_id' => $targetObjectId])
                    ->andWhere(['IN', 'ut_status_id', $utStatusIds])
                    ->groupBy(['ut_task_list_id'])
            ], 'tl_id = user_task_query.ut_task_list_id')
            ->where(['tl_object' => $taskObject])
            ->distinct();
    }
}
