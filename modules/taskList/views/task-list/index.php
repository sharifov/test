<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\taskList\src\entities\taskList\TaskList;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\taskList\src\entities\taskList\search\TaskListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Task Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Task List Item', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => static function (TaskList $model) {
            if ($model->tl_enable_type === TaskList::ET_DISABLED) {
                return ['class' => 'danger'];
            }
        },
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'tl_id',
                'value' => static function (TaskList $model) {
                    return $model->tl_id;
                },
                'options' => ['style' => 'width:100px']
            ],
            //'tl_id',
            'tl_title',
//            'tl_object',
            [
                'attribute' => 'tl_object',
//                'value' => static function (TaskList $model) {
//                    return '<b title="' . Html::encode($model->ff_description) . '" data-toggle="tooltip">' .
//                        ($model->tl_object ? '<i class="fa fa-info-circle info"></i> ' : '') .
//                        Html::encode($model->tl_object) . '</b>';
//                },
//                'format' => 'raw',
            ],
            'tl_condition',
            //'tl_condition_json',
            //'tl_params_json',
            'tl_work_start_time_utc',
            'tl_work_end_time_utc',
            'tl_duration_min',
//            'tl_enabled',
            [
                'attribute' => 'tl_enable_type',
                'value' => static function (TaskList $model) {
                    return $model->getEnableTypeLabel();
                },
                'format' => 'raw',
                'filter' => TaskList::getEnableTypeList()
            ],
            [
                'attribute' => 'tl_cron_expression',
                'value' => static function (TaskList $model) {
                    return $model->tl_cron_expression ?
                        Html::tag('pre', Html::encode($model->tl_cron_expression)) : '-';
                },
                'format' => 'raw',
            ],
            // 'tl_cron_expression',
//            'tl_sort_order',
//            'tl_updated_dt',
//            'tl_updated_user_id',

            [
                'attribute' => 'tl_sort_order',
                'options' => ['style' => 'width:100px']
            ],

            ['class' => UserSelect2Column::class, 'attribute' => 'tl_updated_user_id', 'relation' => 'tlUpdatedUser'],
            ['class' => DateTimeColumn::class, 'attribute' => 'tl_updated_dt'],

            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, TaskList $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'tl_id' => $model->tl_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
