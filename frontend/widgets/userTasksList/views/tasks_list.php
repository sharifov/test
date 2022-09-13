<?php

/**
 * @var Lead $lead
 * @var array $shiftScheduleEventTasks
 * @var yii\web\View $this
 * @var array $userShiftSchedules
 * @var array $shiftScheduleTasks
 * @var Pagination $shiftScheduleTasksPagination
 * @var int|null $activeShiftScheduleId
 * @var string $pjaxUrl
 * @var string $userTimeZone
 * @var array $userShiftSchedulesList
 * @var string $addNoteUrl
 */

use common\models\Lead;
use yii\bootstrap4\LinkPager;
use yii\data\Pagination;
use yii\widgets\Pjax;
use modules\taskList\src\entities\userTask\UserTask;
use yii\helpers\Url;
use modules\featureFlag\FFlag;
use frontend\widgets\userTasksList\helpers\UserTasksListHelper;

?>

    <div class="lead-user-tasks x_panel">
        <!-- Title -->
        <div class="lead-user-tasks__title-wrap lead-user-tasks-title x_title clearfix">
            <div class="row">
                <!-- Title of block -->
                <div class="col-9 lead-user-tasks-title__left">
                    <h2 class="lead-user-tasks-title__title">
                        <i class="fa fa-list-ul"></i> Task List
                    </h2>
                </div>

                <!--  Link to My Tasks -->
                <div class="col-3 lead-user-tasks-title__right">
                    <a target="_blank" href="<?= Url::to(['/task-list/index']); ?>" class="lead-user-tasks-title__my-tasks">
                        <i class="fa fa-external-link lead-user-tasks-title__my-tasks-icon" aria-hidden="true"></i>
                        <span class="lead-user-tasks-title__my-tasks-text">My tasks</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Timelines with list of tasks -->
        <?php if (!empty($userShiftSchedulesList)) : ?>
            <?php $calcPagination = UserTasksListHelper::calcPagination($shiftScheduleTasksPagination); ?>
            <?php Pjax::begin([
                'id' => 'lead-user-tasks__content',
                'enablePushState' => false,
            ]); ?>

            <div class="lead-user-tasks__content x_content">
                <!-- Timeline -->
                <div class="lead-user-tasks-timeline mb-3">
                    <div class="row">
                        <!-- Dates -->
                        <div class="col-8 pl-2 pr-2 lead-user-tasks-timeline__left">
                            <ul class="nav lead-user-tasks-timeline__schedule">
                                <?php foreach ($userShiftSchedulesList as $key => $userShiftSchedule) : ?>
                                    <li class="lead-user-tasks-timeline__nav-item nav-item">
                                        <a href="<?= $pjaxUrl . '&userShiftScheduleId=' . $key; ?>" class="lead-user-tasks-timeline__nav-link rounded-top text-center <?= ($activeShiftScheduleId == $key ? 'active' : ''); ?> <?= ($userShiftSchedule['isDayToday'] ? 'day-today' : ''); ?>">
                                            <p class="lead-user-tasks-timeline__title"> <?= $userShiftSchedule['mainTabTitle']; ?></p>
                                            <p class="lead-user-tasks-timeline__subtitle"><?= $userShiftSchedule['subTabTitle']; ?></p>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- History and Tasks btns-->
                        <div class="col-4 lead-user-tasks-timeline__right">
                            <?php /** @fflag FFlag::FF_KEY_LEAD_TASK_LIST_HISTORY_MODAL_ENABLE, Enable modal in lead view with history of task */ ?>
                            <?php if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_LEAD_TASK_LIST_HISTORY_MODAL_ENABLE)) : ?>
                                <a href="#" class="lead-user-tasks-timeline__btn btn btn-light ml-2" id="task-list-modal-btn" data-url="<?= Url::to(['lead/ajax-get-user-task', 'leadID' => $lead->id]); ?>">
                                    <i class="fa fa-history" aria-hidden="true"></i> History
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- List of tasks-->
                <div class="lead-user-tasks__tasks lead-user-tasks-list">
                    <div id="user-shift-schedule-<?= $activeShiftScheduleId; ?>" class="tab-pane fade in active show">
                        <p class="lead-user-tasks-list__work-started">Work shift started: <?= $userShiftSchedulesList[$activeShiftScheduleId]['title'] ?? ''; ?></p>

                        <table class="lead-user-tasks-table table">
                            <thead class="lead-user-tasks-table__thead">
                                <tr>
                                    <th class="lead-user-tasks-table__status-head lead-user-tasks-table__th"></th>
                                    <th class="lead-user-tasks-table__number-head lead-user-tasks-table__th">№</th>
                                    <th class="lead-user-tasks-table__task-head lead-user-tasks-table__th">Task</th>
                                    <th class="lead-user-tasks-table__start-head lead-user-tasks-table__th">Start</th>
                                    <th class="lead-user-tasks-table__deadline-head lead-user-tasks-table__th">Deadline</th>
                                    <th class="lead-user-tasks-table__completed-head lead-user-tasks-table__th">Completed</th>
                                    <th class="lead-user-tasks-table__note-head lead-user-tasks-table__th">Notes</th>
                                </tr>
                            </thead>

                            <!-- Table body with tasks list -->
                            <tbody class="lead-user-tasks-table__tbody">
                            <?php $iterationNumb = $calcPagination['from']; ?>
                            <?php foreach ($shiftScheduleTasks as $userTask) : ?>
                                <?php
                                    $isDeadline = UserTasksListHelper::isDeadline($userTask['ut_end_dt'], $userTask['ut_status_id'], $userTimeZone);
                                    $statusName = $isDeadline ? 'failed' : UserTask::STATUS_LIST[$userTask['ut_status_id']];
                                ?>
                                <tr class="lead-user-tasks-table__row lead-user-tasks-table__<?= strtolower($statusName); ?>" style="background-color: <?= UserTasksListHelper::getColorByStatusAndDeadline((int)$userTask['ut_status_id'], $isDeadline); ?>">
                                    <td class="text-center lead-user-tasks-table__status lead-user-tasks-table__col">
                                        <?= UserTasksListHelper::renderStatusIcon((int)$userTask['ut_status_id'], $isDeadline); ?>
                                    </td>
                                    <th class="lead-user-tasks-table__number lead-user-tasks-table__col">
                                        <?= $iterationNumb++; ?>
                                    </th>
                                    <td class="lead-user-tasks-table__task lead-user-tasks-table__col">
                                        <?= $userTask['tl_title'] ?? ''; ?>
                                    </td>
                                    <td class="lead-user-tasks-table__start lead-user-tasks-table__col">
                                        <?= UserTasksListHelper::renderStartDate($userTask['ut_status_id'], $userTask['ut_start_dt'], $isDeadline, $userTimeZone); ?>
                                    </td>
                                    <td class="lead-user-tasks-table__deadline lead-user-tasks-table__col">
                                        <?= UserTasksListHelper::renderDeadlineStatus($userTask['ut_status_id'], $userTask['ut_start_dt'], $userTask['ut_end_dt'], $userTimeZone); ?>
                                    </td>
                                    <td class="lead-user-tasks-table__completed lead-user-tasks-table__col">
                                        <?= UserTasksListHelper::renderCompletedStatus($userTask['ut_status_id'], $userTask['complete_time'], $userTimeZone); ?>
                                    </td>

                                    <td class="lead-user-tasks-table__note lead-user-tasks-table__col">
                                        <?php
                                            $description = $userTask['ut_description'] ?? null;
                                            echo UserTasksListHelper::renderNote($userTask['ut_user_id'], $userTask['ut_id'], $description);
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="lead-user-tasks-list__pagination-wrap mt-3">
                        <div class="row">
                            <div class="col-6">
                                Showing <?= $calcPagination['from']; ?> to <?= $calcPagination['to']; ?> of <?= $shiftScheduleTasksPagination->totalCount; ?> tasks
                            </div>
                            <div class="col-6">
                                <div class="lead-user-tasks-list__pagination">
                                    <?= LinkPager::widget([
                                        'pagination' => $shiftScheduleTasksPagination,
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php Pjax::end(); ?>
        <?php else : ?>
            <div class="lead-user-tasks__not-tasks text-center">
                Not found user tasks
            </div>
        <?php endif; ?>
    </div>

<?php
$js = <<<JS
    starTimers();
    startTooltips();
    
    $(document).on('pjax:complete', function() {
        starTimers();
        startTooltips();
    });

    $(document).on('click', '.js-add_note_task_list', function() {
        let modal = $('#modal-sm');
        modal.addClass('lead-user-tasks-modal');
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
