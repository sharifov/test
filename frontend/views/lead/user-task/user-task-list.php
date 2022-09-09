<?php

/** @var View $this */
/** @var UserTaskSearch $searchModel */
/** @var \yii\data\Pagination $pagination */
/** @var array $historyTasks */
/** @var integer $leadID */

use src\auth\Auth;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\web\View;
use dosamigos\datepicker\DatePicker;
use modules\taskList\src\entities\userTask\UserTask;
use yii\bootstrap4\ActiveForm;
use yii\widgets\{
    Pjax,
    LinkPager
};
use modules\taskList\src\entities\userTask\UserTaskSearch;
use frontend\widgets\userTasksList\helpers\UserTasksListHelper;

$userTimezone = 'UTC';
$calcPagination = UserTasksListHelper::calcPagination($pagination);
?>

<div class="user-task-history">
<?php Pjax::begin(['id' => 'pjax-user-task-list', 'timeout' => 5000, 'enablePushState' => false]); ?>

    <!-- Form with date -->
    <?php $form = ActiveForm::begin([
        'id' => 'search-user-task-list-form',
        'action' => ['/lead/ajax-get-user-task', 'leadID' => $leadID],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
        ],
    ]); ?>
        <div class="user-task-history-date">
            <div class="row">
                <div class="col-md-2 user-task-history-date__column">
                    <?= $form->field($searchModel, 'ut_start_dt')->widget(DatePicker::class, [
                        'options' => [
                            'placeholder' => 'Choose Date',
                            'onchange' => "$('#search-user-task-list-form').submit();",
                        ],
                        'clientOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'clearBtn' => true,
                            'autoclose' => true,
                        ]
                    ])->label(false) ?>
                </div>
            </div>
        </div>
    <?php ActiveForm::end(); ?>

    <!-- List of tasks -->
    <?php if (!empty($historyTasks)) : ?>
        <table class="user-task-history-table table">
            <thead class="user-task-history-table__thead">
                <tr>
                    <th class="user-task-history-table__thead-status-icon user-task-history-table__th"></th>
                    <th class="user-task-history-table__thead-number user-task-history-table__th">№</th>
                    <th class="user-task-history-table__thead-task user-task-history-table__th">Task</th>
                    <th class="user-task-history-table__thead-start user-task-history-table__th">Start</th>
                    <th class="user-task-history-table__thead-deadline user-task-history-table__th">Deadline</th>
                    <th class="user-task-history-table__thead-completed user-task-history-table__th">Completed</th>
                    <th class="user-task-history-table__thead-note user-task-history-table__th">Notes</th>
                </tr>
            </thead>

            <tbody class="user-task-history-table__tbody">
                <?php $iterationNumb = $calcPagination['from']; ?>
                <?php foreach ($historyTasks as $task) :
                    $task = (object)$task;
                    $isDeadline = UserTasksListHelper::isDeadline($task->ut_end_dt, $task->ut_status_id, $userTimezone);
                    $statusName = $isDeadline ? 'failed' : UserTask::STATUS_LIST[(int)$task->ut_status_id];
                    ?>
                    <tr class="user-task-history-table__row-<?= strtolower($statusName); ?>" style="background-color: <?= UserTasksListHelper::getColorByStatusAndDeadline((int)$task->ut_status_id, $isDeadline); ?>">
                        <td class="user-task-history-table__status-icon">
                            <?= UserTasksListHelper::renderStatusIcon((int)$task->ut_status_id, $isDeadline); ?>
                        </td>
                        <td class="user-task-history-table__number">
                            <?= $iterationNumb++; ?>
                        </td>
                        <td class="user-task-history-table__task">
                            <?= $task->tl_title ?: ''; ?>
                        </td>
                        <td class="user-task-history-table__start">
                            <?= UserTasksListHelper::renderStartDate($task->ut_status_id, $task->ut_start_dt, $isDeadline, $userTimezone); ?>
                        </td>
                        <td class="user-task-history-table__deadline">
                            <?= UserTasksListHelper::renderDeadlineStatus($task->ut_status_id, $task->ut_start_dt, $task->ut_end_dt, $userTimezone); ?>
                        </td>
                        <td class="user-task-history-table__completed">
                            <?= UserTasksListHelper::renderCompletedStatus($task->ut_status_id, $task->complete_time, $userTimezone); ?>
                        </td>
                        <th class="user-task-history-table__note">
                            <?php if (!empty($task->ut_description)) {
                                    echo Html::tag('span', StringHelper::truncate($task->ut_description, 15), [
                                        'class' => 'js-tooltip',
                                        'data' => [
                                            'custom-class' => 'lead-user-tasks-table__note-tooltip',
                                            'original-title' => $task->ut_description,
                                        ]
                                    ]);
                            } else {
                                echo '<span class="user-task-history-table__no-notes">No notes</span>';
                            } ?>
                        </th>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="user-task-history-pagination">
            <div class="row">
                <div class="col-6 user-task-history-pagination__text">
                    Showing <?= $calcPagination['from']; ?> to <?= $calcPagination['to']; ?> of <?= $pagination->totalCount; ?> tasks
                </div>
                <div class="col-6 user-task-history-pagination__paginate">
                    <?= LinkPager::widget([
                        'pagination' => $pagination,
                    ]); ?>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="user-task-history__no-tasks text-center">No tasks</div>
    <?php endif;?>

<?php Pjax::end(); ?>
</div>


<?php
$js = <<<JS
    starTimers();
    startTooltips();
    
    $(document).on('pjax:complete', function() {
        starTimers();
        startTooltips();
    });
JS;

$this->registerJs($js);