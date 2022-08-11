<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\search\ShiftScheduleTypeLabelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shift Schedule Type Labels';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-schedule-type-label-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Shift Schedule Type Label', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => static function (ShiftScheduleTypeLabel $model) {
            if (!$model->stl_enabled) {
                return ['class' => 'danger'];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'stl_key',
            [
                'attribute' => 'stl_key',
                'value' => static function (ShiftScheduleTypeLabel $model) {
                    return Html::tag(
                        'span',
                        $model->stl_key,
                        ['class' => 'label label-default']
                    );
                },
                'format' => 'raw',
                'options' => ['style' => 'width:200px']
            ],
            'stl_name',
            'stl_enabled:boolean',
            [
                'label' => 'Color',
                'value' => static function (ShiftScheduleTypeLabel $model) {
                    return $model->stl_color ? Html::tag(
                        'span',
                        '&nbsp;&nbsp;&nbsp;',
                        ['class' => 'label', 'style' => 'background-color: ' . $model->stl_color]
                    ) : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width:60px']
            ],
            'stl_color',
            [
                'label' => 'Icon',
                'value' => static function (ShiftScheduleTypeLabel $model) {
                    return $model->stl_icon_class ? Html::tag(
                        'i',
                        '',
                        ['class' => $model->stl_icon_class] // , 'style' => 'color: ' . $model->sst_color
                    ) : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width:50px']
            ],
            'stl_icon_class',




            //'stl_params_json',

            [
                'attribute' => 'stl_sort_order',
                'options' => ['style' => 'width:100px']
            ],

            ['class' => UserSelect2Column::class, 'attribute' => 'stl_updated_user_id', 'relation' => 'stlUpdatedUser'],
            ['class' => DateTimeColumn::class, 'attribute' => 'stl_updated_dt'],

            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, ShiftScheduleTypeLabel $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'stl_key' => $model->stl_key]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
