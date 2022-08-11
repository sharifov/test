<?php

use common\components\grid\DateTimeColumn;
use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shift Schedule Event Tasks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-schedule-event-task-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Shift Schedule Event Task', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-ShiftScheduleEventTask']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],

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
                    if ($model->ssetUserTask === null) {
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
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'sset_created_dt',
                'format' => 'byUserDateTime',
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => static function ($action, ShiftScheduleEventTask $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'sset_event_id' => $model->sset_event_id, 'sset_user_task_id' => $model->sset_user_task_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
