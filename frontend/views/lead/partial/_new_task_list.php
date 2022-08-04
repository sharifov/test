<?php

/**
 * @var \common\models\Lead $lead
 */

use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\userTask\UserTask;
use src\auth\Auth;

$shiftScheduleEventTasks = ShiftScheduleEventTask::find()
    ->select(['shift_schedule_event_task.*'])
    ->joinWith('userShiftSchedule')
    ->innerJoin([
        'user_task_query' => UserTask::find()
            ->select(['ut_id'])
            ->andWhere(['ut_target_object' => TargetObject::TARGET_OBJ_LEAD])
            ->andWhere(['ut_target_object_id' => $lead->id])
    ], 'ut_id = shift_schedule_event_task.sset_user_task_id')
    ->asArray()
    ->all();

$userShiftSchedules = [];

foreach ($shiftScheduleEventTasks as $shiftScheduleEvent) {
    $userShiftSchedules[$shiftScheduleEvent['sset_event_id']] = UserShiftScheduleHelper::getDataForTaskList($shiftScheduleEvent['userShiftSchedule'], Auth::user()->timezone);
}


$shiftScheduleEventTasks = \yii\helpers\ArrayHelper::map($shiftScheduleEventTasks, 'sset_user_task_id', function ($item) {
    return $item;
}, 'sset_event_id');

?>


<div class="x_panel" id="task-list" style="display: block">
    <div class="x_title">
        <h2><i class="fa fa-list-ul"></i> Task List</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="" style="display: block;">
        <div class="multiple-items1" style="width: 100%; display: block">
            <?php foreach ($userShiftSchedules as $key => $userShiftSchedule) : ?>
                <div style="width: 150px;">
                    sdsds
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
