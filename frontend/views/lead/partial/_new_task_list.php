<?php

/**
 * @var \common\models\Lead $lead
 * @var array $shiftScheduleEventTasks
 * @var $this yii\web\View
 */

use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
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
\frontend\assets\TaskListAssets::register($this);
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
            <ul class="nav nav-tabs">
                <?php $i = 0 ?>
                <?php foreach ($userShiftSchedules as $key => $userShiftSchedule) : ?>
                    <?php if ($i === 0) {
                        $i = $key;
                    } ?>
                    <li class="nav-item">
                        <a data-toggle="tab"
                           href="#tab-<?= $key ?>"
                           class="nav-link text-center <?= ($i === $key ? 'active' : '') ?>">
                            <span class="task-list_nab-title"> <?= $userShiftSchedule['mainTabTitle'] ?> </span> <br>
                            <span class="task-list_nab-subtitle">  <?= $userShiftSchedule['subTabTitle'] ?> </span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="tab-content">
                <?php foreach ($shiftScheduleEventTasks as $key => $shiftScheduleEventTask) : ?>
                    <?php $userShiftSchedule = $userShiftSchedules[$key] ?? [] ?>
                    <div id="tab-<?= $key ?>" class="tab-pane fade in <?= ($i === $key ? 'active show' : '') ?>">
                        <div class="my-task-list-wrap">
                            <a target="_blank" href="<?= \yii\helpers\Url::to(['/task-list/index']) ?>"
                               class="my-task-list-link"> My tasks </a>
                            <span class="my-task-list-start">Started: <?= $userShiftSchedule['title'] ?? '' ?></span>
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
                                            $description = $userTask['ut_description'] ?? null;
                                            ?>
                                            <a href="javascript:void(0)" class="js-add_note_task_list"
                                               data-usertaskid="<?= $userTask['ut_id'] ?>"
                                               data-new-note="<?= empty($description) ?>"
                                               title="<?= $description ?>"
                                            >
                                                <?= empty($description) ? 'Add note' : \yii\helpers\StringHelper::truncate($description, 15) ?>
                                            </a>
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
