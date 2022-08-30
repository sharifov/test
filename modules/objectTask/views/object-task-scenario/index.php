<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\objectTask\src\abac\ObjectTaskObject;
use modules\objectTask\src\entities\ObjectTaskScenario;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\objectTask\src\entities\ObjectTaskScenarioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Object Task Scenarios';
$this->params['breadcrumbs'][] = $this->title;
/** @abac ObjectTaskObject::ACT_OBJECT_TASK_SCENARIO, ObjectTaskObject::ACTION_UPDATE, Access to page /object-task/object-task-scenario/create */
$canCreate = \Yii::$app->abac->can(null, ObjectTaskObject::ACT_OBJECT_TASK_SCENARIO, ObjectTaskObject::ACTION_UPDATE);
?>
<div class="object-task-scenario-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($canCreate) : ?>
        <p>
            <?= Html::a('Create Object Task Scenario', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif; ?>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'ots_id',
            'ots_key',
            ['class' => DateTimeColumn::class, 'attribute' => 'ots_updated_dt'],
            ['class' => UserSelect2Column::class, 'attribute' => 'ots_updated_user_id', 'relation' => 'otsUpdatedUser'],
            [
                'attribute' => 'ots_enable',
                'format' => 'booleanByLabel',
                'filter' => [1 => 'Enable', 0 => 'Disable'],
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, ObjectTaskScenario $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'ots_id' => $model->ots_id]);
                },
                'visibleButtons' => [
                    'update' => static function (ObjectTaskScenario $model, $key, $index) {
                        /** @abac ObjectTaskObject::ACT_OBJECT_TASK_SCENARIO, ObjectTaskObject::ACTION_UPDATE, Access to page /object-task/object-task-scenario/update */
                        return \Yii::$app->abac->can(
                            null,
                            ObjectTaskObject::ACT_OBJECT_TASK_SCENARIO,
                            ObjectTaskObject::ACTION_UPDATE
                        );
                    },
                    'delete' => static function (ObjectTaskScenario $model, $key, $index) {
                        /** @abac ObjectTaskObject::ACT_OBJECT_TASK_SCENARIO, ObjectTaskObject::ACTION_UPDATE, Access to page /object-task/object-task-scenario/delete */
                        return \Yii::$app->abac->can(
                            null,
                            ObjectTaskObject::ACT_OBJECT_TASK_SCENARIO,
                            ObjectTaskObject::ACTION_DELETE
                        );
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
