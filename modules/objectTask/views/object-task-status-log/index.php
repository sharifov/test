<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\objectTask\src\abac\ObjectTaskObject;
use modules\objectTask\src\entities\ObjectTask;
use modules\objectTask\src\entities\ObjectTaskStatusLog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\objectTask\src\entities\ObjectTaskStatusLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Object Task Status Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="object-task-status-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'otsl_id',
            'otsl_ot_uuid',
            [
                'attribute' => 'otsl_old_status',
                'value' => static function (ObjectTaskStatusLog $model) {
                    return ObjectTask::STATUS_LIST[$model->otsl_old_status] ?? ' - ';
                },
                'filter' => ObjectTask::STATUS_LIST,
            ],
            [
                'attribute' => 'otsl_new_status',
                'value' => static function (ObjectTaskStatusLog $model) {
                    return ObjectTask::STATUS_LIST[$model->otsl_new_status] ?? ' - ';
                },
                'filter' => ObjectTask::STATUS_LIST,
            ],
            'otsl_description',
            ['class' => UserSelect2Column::class, 'attribute' => 'otsl_created_user_id', 'relation' => 'otslCreatedUser'],
            ['class' => DateTimeColumn::class, 'attribute' => 'otsl_created_dt'],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, ObjectTaskStatusLog $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'otsl_id' => $model->otsl_id]);
                },
                'visibleButtons' => [
                    'update' => static function (ObjectTaskStatusLog $model, $key, $index) {
                        /** @abac ObjectTaskObject::ACT_OBJECT_TASK_STATUS_LOG, ObjectTaskObject::ACTION_UPDATE, Access to page /object-task/object-task-status-log/update */
                        return \Yii::$app->abac->can(
                            null,
                            ObjectTaskObject::ACT_OBJECT_TASK_STATUS_LOG,
                            ObjectTaskObject::ACTION_UPDATE
                        );
                    },
                    'delete' => static function (ObjectTaskStatusLog $model, $key, $index) {
                        /** @abac ObjectTaskObject::ACT_OBJECT_TASK_STATUS_LOG, ObjectTaskObject::ACTION_UPDATE, Access to page /object-task/object-task-status-log/delete */
                        return \Yii::$app->abac->can(
                            null,
                            ObjectTaskObject::ACT_OBJECT_TASK_STATUS_LOG,
                            ObjectTaskObject::ACTION_DELETE
                        );
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
