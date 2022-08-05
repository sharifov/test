<?php

/**
 * @var \common\models\Lead $lead
 * @var array $shiftScheduleEventTasks
 * @var $this yii\web\View
 */

use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use modules\taskList\abac\dto\TaskListAbacDto;
use modules\taskList\abac\TaskListAbacObject;
use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskHelper;
use src\auth\Auth;
use yii\helpers\Url;

$userShiftSchedules = [];

foreach ($shiftScheduleEventTasks as $shiftScheduleEvent) {
    $userShiftSchedules[$shiftScheduleEvent['sset_event_id']] = UserShiftScheduleHelper::getDataForTaskList($shiftScheduleEvent['userShiftSchedule'], Auth::user()->timezone);
}


$shiftScheduleEventTasks = \yii\helpers\ArrayHelper::map($shiftScheduleEventTasks, 'sset_user_task_id', function ($item) {
    return $item;
}, 'sset_event_id');
?>


    <div class="x_panel task-list_wrap" id="task-list">
        <div class="x_title">
            <h2><i class="fa fa-list-ul"></i> Task List</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: block;">
            <div style="display: flex">
                <ul class="nav nav-tabs">
                    <?php $i = 0 ?>
                    <?php foreach ($userShiftSchedules as $key => $userShiftSchedule) : ?>
                        <?php if ($i === 0) {
                            $i = $key;
                        } ?>
                        <li class="nav-item" style="max-width: 20%;">
                            <a data-toggle="tab"
                               href="#tab-<?= $key ?>"
                               class="nav-link text-center <?= ($i === $key ? 'active' : '') ?>">
                                <span class="task-list_nab-title"> <?= $userShiftSchedule['mainTabTitle'] ?> </span> <br>
                                <span class="task-list_nab-subtitle" style="font-size: 10px">  <?= $userShiftSchedule['subTabTitle'] ?> </span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php /** @fflag FFlag::FF_KEY_LEAD_TASK_LIST_HISTORY_MODAL_ENABLE, Enable modal in lead view with history of task */ ?>
                <?php if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_LEAD_TASK_LIST_HISTORY_MODAL_ENABLE)) : ?>
                    <a href="#" class="task-list-history-btn" id="task-list-modal-btn" data-url="<?= Url::to(['lead/ajax-get-user-task', 'leadID' => $lead->id]) ?>">
                        <i class="fa fa-history" aria-hidden="true" style="padding-right: 5px"></i>  History</a>
                <?php endif; ?>
            </div>


            <div class="tab-content">
                <?php foreach ($shiftScheduleEventTasks as $key => $shiftScheduleEventTask) : ?>
                    <?php $userShiftSchedule = $userShiftSchedules[$key] ?? [] ?>
                    <div id="tab-<?= $key ?>" class="tab-pane fade in <?= ($i === $key ? 'active show' : '') ?>">
                        <div class="my-task-list-wrap">
                            <a target="_blank" href="<?= \yii\helpers\Url::to(['/task-list/index']) ?>"
                               class="my-task-list-link"> <i class="fa fa-tasks" aria-hidden="true"></i> My tasks </a>
                            <span class="my-task-list-start" style="padding-left: 15px">Started: <?= $userShiftSchedule['title'] ?? '' ?></span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th style="width: 50px"></th>
                                    <th>Task</th>
                                    <th>Deadline</th>
                                    <th>Notes</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($shiftScheduleEventTask as $userTask) : ?>
                                    <tr>
                                        <td style="width: 50px" class="text-center">
                                            <?= UserTaskHelper::renderStatus($userTask['ut_status_id']) ?>
                                        </td>
                                        <td><?= $userTask['tl_title'] ?? '' ?></td>
                                        <td></td>
                                        <td>
                                            <?php
                                            $dto = new TaskListAbacDto();
                                            $dto->setIsUserTaskOwner((int)$userTask['ut_user_id'] === Auth::id());
                                            $description = $userTask['ut_description'] ?? null;

                                            /** @abac TaskListAbacObject::OBJ_USER_TASK, TaskListAbacObject::ACTION_ADD_NOTE, Access to add UserTask Note */
                                            if (Yii::$app->abac->can($dto, TaskListAbacObject::OBJ_USER_TASK, TaskListAbacObject::ACTION_ADD_NOTE)) :?>
                                                <a href="javascript:void(0)" class="js-add_note_task_list"
                                                   data-usertaskid="<?= $userTask['ut_id'] ?>"
                                                   data-new-note="<?= empty($description) ?>"
                                                   title="<?= $description ?>"
                                                >
                                                    <?= empty($description) ? 'Add note' : \yii\helpers\StringHelper::truncate($description, 15) ?>
                                                </a>
                                            <?php else : ?>
                                                <span
                                                        title="<?= $description ?>">
                                                    <?= \yii\helpers\StringHelper::truncate($description, 15) ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                </tbody>
                            </table>
                        </div>

                    </div>


                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php

$addNoteUrl = Url::to(['/task-list/ajax-add-note']);
$js = <<<JS
       $(document).on('click', '.js-add_note_task_list', function() {
            let modal = $('#modal-sm');
            modal.find('.modal-title').html($(this).data('new-note') == '1'? 'Add note' : 'Edit note');
            modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
            
            let userTaskId = $(this).data('usertaskid');
            $.get('{$addNoteUrl}' + '?userTaskId=' + userTaskId, function(data) {
               modal.find('.modal-body').html(data);
            }).fail(function (xhr) {
                 setTimeout(function () {
                     modal.modal('hide');
                     createNotify('Error', xhr.statusText, 'error');
                     }, 800);
            });
});
JS;

$this->registerJs($js);

?>
<style>
    .task-list_wrap .nav-tabs {
        height: 70px;
        overflow-x: scroll;
        overflow-y: hidden;
        flex-direction: column;
        width: 80%;
    }

    .task-list_wrap .nav-link {
        color: #596B7D;
        font-size: 12px;
        padding: 8px 15px 5px;
        border-radius: 2px 2px 0 0;
    }

    .task-list_wrap .my-task-list-wrap {
        padding: 20px 0 12px;
    }

    .task-list_wrap .task-list-history-btn{
        display: flex;
        justify-content: center;
        align-items: center;
        margin-left: 15px;
    }

</style>
