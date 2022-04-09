<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\shiftSchedule\src\entities\shiftScheduleType\search\ShiftScheduleTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shift Schedule Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-schedule-type-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Shift Schedule Type', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => static function (ShiftScheduleType $model) {
            if (!$model->sst_enabled) {
                return ['class' => 'danger'];
            }
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'sst_id',
                'value' => static function (ShiftScheduleType $model) {
                    return $model->sst_id;
                },
                'options' => ['style' => 'width:100px']
            ],
//            [
//                'attribute' => 'al_action',
//                'value' => function (\common\models\ApiLog $model) {
//                    return '<b>' . Html::encode($model->al_action) . '</b>';
//                },
//                'format' => 'raw',
//                'filter' => \common\models\ApiLog::getActionFilter(Yii::$app->request->isPjax)
//            ],
//            'sst_id',
            [
                'label' => 'icon',
                'value' => static function (ShiftScheduleType $model) {
                    return $model->sst_icon_class ? Html::tag(
                        'i',
                        '',
                        ['class' => $model->sst_icon_class] // , 'style' => 'color: ' . $model->sst_color
                    ) : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width:50px']
            ],
            [
                'label' => 'color',
                'value' => static function (ShiftScheduleType $model) {
                    return $model->sst_color ? Html::tag(
                        'span',
                        '&nbsp;&nbsp;&nbsp;',
                        ['class' => 'label', 'style' => 'background-color: ' . $model->sst_color]
                    ) : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width:60px']
            ],
            'sst_key',
            'sst_name',
            'sst_title',
            'sst_enabled:boolean',
            'sst_readonly:boolean',
            'sst_work_time:boolean',
            //'sst_color',
            [
                'attribute' => 'sst_color',
                'value' => static function (ShiftScheduleType $model) {
                    return $model->sst_color ? Html::tag(
                        'span',
                        '&nbsp;&nbsp;&nbsp;&nbsp;',
                        ['class' => 'label', 'style' => 'background-color: ' . $model->sst_color]
                    ) . ' ' .
                        $model->sst_color : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width:180px']
            ],
            [
                'attribute' => 'sst_color',
                'value' => static function (ShiftScheduleType $model) {
                    return $model->sst_icon_class ? Html::tag(
                        'i',
                        '',
                        ['class' => $model->sst_icon_class] // , 'style' => 'color: ' . $model->sst_color
                    ) . ' &nbsp;&nbsp;&nbsp;' .
                        $model->sst_icon_class : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width:180px']
            ],
//            'sst_icon_class',
            //'sst_css_class',
            //'sst_params_json',
//            'sst_sort_order',
            [
                'attribute' => 'sst_sort_order',
                'options' => ['style' => 'width:100px']
            ],

            ['class' => UserSelect2Column::class, 'attribute' => 'sst_updated_user_id', 'relation' => 'sstUpdatedUser'],
            ['class' => DateTimeColumn::class, 'attribute' => 'sst_updated_dt'],

            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, ShiftScheduleType $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'sst_id' => $model->sst_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
