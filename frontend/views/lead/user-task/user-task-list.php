<?php

/** @var \yii\web\View $this */
/** @var \modules\taskList\src\entities\userTask\UserTaskSearch $searchModel */
/** @var \yii\data\ActiveDataProvider $dataProvider */
/** @var integer $leadID */

use dosamigos\datepicker\DatePicker;
use modules\taskList\src\entities\userTask\UserTask;
use yii\bootstrap4\ActiveForm;
use yii\grid\GridView;
use yii\widgets\Pjax;

?>
<style>
    .datepicker {
        z-index: 1051!important;
    }
</style>
<div class="user-task-list">

    <?php Pjax::begin(['id' => 'pjax-user-task-list', 'timeout' => 5000, 'enablePushState' => false]); ?>

    <?php $form = ActiveForm::begin([
        'id' => 'search-user-task-list-form',
        'action' => ['/lead/ajax-get-user-task', 'leadID' => $leadID],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

        <div class="row">
            <div class="col-md-2">
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

    <?php ActiveForm::end(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{errors}\n{items}\n<div class='d-flex justify-content-between'><div>{summary}</div><div>{pager}</div></div>",
        'columns' => [
            [
                'label' => 'Task',
                'value' => static function (UserTask $model) {
                    return $model->taskList->tl_title;
                },
            ],
            [
                'label' => 'Agent',
                'value' => static function (UserTask $model) {
                    return $model->user->username;
                },
            ],
            [
                'label' => 'Completed Time',
                'value' => static function (UserTask $model) {
                    if ($model->ut_status_id === UserTask::STATUS_COMPLETE) {
                        $log = $model->getLastStatusLogByStatusId(
                            UserTask::STATUS_COMPLETE
                        );

                        if ($log !== null) {
                            return $log->utsl_created_dt;
                        }
                    }

                    return '';
                },
                'enableSorting' => false,
            ],
            [
                'label' => 'Status',
                'value' => static function (UserTask $model) {
                    return UserTask::STATUS_LIST[$model->ut_status_id];
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
