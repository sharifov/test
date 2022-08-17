<?php

use modules\objectTask\src\entities\ObjectTask;
use modules\objectTask\src\services\ObjectTaskService;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\objectTask\src\entities\ObjectTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Object Tasks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="object-task-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Object Task', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'ot_uuid',
            'ot_q_id',
            [
                'attribute' => 'ot_ots_id',
                'label' => 'Scenario',
                'value' => static function (ObjectTask $model) {
                    return ObjectTaskService::SCENARIO_LIST[$model->objectTaskScenario->ots_key];
                }
            ],
            'ot_group_hash',
            'ot_object',
            [
                'attribute' => 'ot_object_id',
                'format' => 'raw',
                'value' => static function (ObjectTask $model) {
                    if ($model->ot_object === ObjectTaskService::OBJECT_LEAD) {
                        return Html::a($model->ot_object_id, "/lead/view/{$model->lead->gid}", [
                            'target' => '_blank',
                            'data' => [
                                'pjax' => 0
                            ],
                        ]);
                    }

                    return $model->ot_object_id;
                }
            ],
            [
                'attribute' => 'ot_command',
                'value' => static function (ObjectTask $model) {
                    return ObjectTaskService::COMMAND_LIST[$model->ot_command];
                },
                'filter' => ObjectTaskService::COMMAND_LIST,
            ],
            [
                'attribute' => 'ot_status',
                'value' => static function (ObjectTask $model) {
                    return ObjectTask::STATUS_LIST[$model->ot_status] ?? '';
                },
                'filter' => ObjectTask::STATUS_LIST,
            ],
            'ot_execution_dt',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, ObjectTask $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'ot_uuid' => $model->ot_uuid]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
