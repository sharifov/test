<?php

use common\components\grid\DateTimeColumn;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\ShiftScheduleTypeLabelAssign;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\search\ShiftScheduleTypeLabelAssignSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shift Schedule Type Label Assigns';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-schedule-type-label-assign-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(
            '<i class="fa fa-plus"></i> Create Shift Schedule Type Label Assign',
            ['create'],
            ['class' => 'btn btn-success']
        ) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
//            'tla_stl_key',
            [
                'attribute' => 'tla_stl_key',
                'value' => static function (ShiftScheduleTypeLabelAssign $model) {
                    return $model->tla_stl_key . ' - ' . $model->getShiftTypeLabel();
                },
                'filter' => \modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel::getList()
                //'options' => ['style' => 'width:100px']
            ],
            //'tla_sst_id',
            [
                'attribute' => 'tla_sst_id',
                'value' => static function (ShiftScheduleTypeLabelAssign $model) {
                    return $model->getShiftTypeName();
                },
                'filter' => \modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType::getList()
                //'options' => ['style' => 'width:100px']
            ],

            ['class' => DateTimeColumn::class, 'attribute' => 'tla_created_dt'],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, ShiftScheduleTypeLabelAssign $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'tla_stl_key' => $model->tla_stl_key, 'tla_sst_id' => $model->tla_sst_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
