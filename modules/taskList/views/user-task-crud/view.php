<?php

use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\userTask\UserTask */

$this->title = $model->ut_id;
$this->params['breadcrumbs'][] = ['label' => 'User Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-task-view">

    <div class="col-md-4">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'ut_id' => $model->ut_id, 'ut_year' => $model->ut_year, 'ut_month' => $model->ut_month], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ut_id' => $model->ut_id, 'ut_year' => $model->ut_year, 'ut_month' => $model->ut_month], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ut_id',
            'ut_user_id:userNameWithId',
            'ut_target_object',
            'ut_target_object_id',
            [
                'attribute' => 'ut_task_list_id',
                'value' => static function (UserTask $model) {
                    if (!$model->ut_task_list_id) {
                        return Yii::$app->formatter->nullDisplay;
                    }
                    return Html::a(
                        $model->taskList->tl_title . ' (' . $model->ut_task_list_id . ')' ?? '-',
                        [
                            'task-list/view',
                            'tl_id' => $model->ut_task_list_id
                        ],
                        ['target' => '_blank', 'data-pjax' => 0]
                    );
                },
                'format' => 'raw',
            ],
            'ut_start_dt:byUserDateTimeAndUTC',
            'ut_end_dt:byUserDateTimeAndUTC',
            [
                'attribute' => 'ut_priority',
                'value' => static function (UserTask $model) {
                    return UserTaskHelper::priorityLabel($model->ut_priority);
                },
                'format' => 'raw',
                'filter' => UserTask::PRIORITY_LIST,
            ],
            [
                'attribute' => 'ut_status_id',
                'value' => static function (UserTask $model) {
                    return UserTaskHelper::statusLabel($model->ut_status_id);
                },
                'format' => 'raw',
                'filter' => UserTask::STATUS_LIST,
            ],
            'ut_created_dt:byUserDateTimeAndUTC',
            'ut_year',
            'ut_month',
        ],
    ]) ?>

    </div>
</div>
