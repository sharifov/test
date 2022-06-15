<?php

use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask */

$this->title = $model->sset_event_id;
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Event Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="shift-schedule-event-task-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'sset_event_id' => $model->sset_event_id, 'sset_user_task_id' => $model->sset_user_task_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'sset_event_id' => $model->sset_event_id, 'sset_user_task_id' => $model->sset_user_task_id], [
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
                [
                    'attribute' => 'sset_event_id',
                    'value' => static function (ShiftScheduleEventTask $model) {
                        if (!$model->sset_event_id) {
                            return Yii::$app->formatter->nullDisplay;
                        }
                        return Html::a(
                            $model->sset_event_id,
                            [
                                '/user-shift-schedule-crud/view',
                                'id' => $model->sset_event_id,
                            ],
                            ['target' => '_blank', 'data-pjax' => 0]
                        );
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'sset_user_task_id',
                    'value' => static function (ShiftScheduleEventTask $model) {
                        if (!$model->sset_user_task_id) {
                            return Yii::$app->formatter->nullDisplay;
                        }
                        return Html::a(
                            $model->sset_user_task_id,
                            [
                                'user-task-crud/view',
                                'ut_id' => $model->sset_user_task_id,
                                'ut_year' => $model->ssetUserTask->ut_year,
                                'ut_month' => $model->ssetUserTask->ut_month,
                            ],
                            ['target' => '_blank', 'data-pjax' => 0]
                        );
                    },
                    'format' => 'raw',
                ],
                'sset_created_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
