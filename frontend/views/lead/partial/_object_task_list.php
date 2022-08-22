<?php

use common\components\grid\DateTimeColumn;
use modules\objectTask\src\entities\ObjectTask;
use modules\objectTask\src\services\ObjectTaskService;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\objectTask\src\entities\ObjectTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

Pjax::begin(['id' => 'pjax-object-task-list', 'timeout' => 5000, 'enablePushState' => false]);
?>

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
        ['class' => DateTimeColumn::class, 'attribute' => 'ot_execution_dt'],
    ],
]); ?>

<?php

Pjax::end();
