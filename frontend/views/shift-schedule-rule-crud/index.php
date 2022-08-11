<?php

use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\shiftSchedule\src\entities\shiftCategory\ShiftCategoryQuery;
use modules\shiftSchedule\src\entities\shiftScheduleRule\search\SearchShiftScheduleRule;
use modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\widget\ShiftSelectWidget;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel SearchShiftScheduleRule */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shift Schedule Rules';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-schedule-rule-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Shift Schedule Rule', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-shift-schedule-rule', 'scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['attribute' => 'ssr_id', 'options' => [
                'style' => 'width: 80px'
            ]],
            [
                'attribute' => 'shift_name',
                'value' => static function (ShiftScheduleRule $model) {
                    return $model->shift ? $model->shift->getColorLabel() . '&nbsp; '
                        . Html::a(
                            $model->shift->sh_name,
                            ['shift-crud/view', 'id' => $model->shift->sh_id],
                            ['data-pjax' => 0]
                        ) : '-';
                },
                'options' => [
                    'style' => 'width: 120px'
                ],
                'format' => 'raw'
            ],
            [
                'attribute' => 'shift_category',
                'value' => function (ShiftScheduleRule $model) {
                    return $model->shift->category->sc_name ?? null;
                },
                'filter' => ShiftCategoryQuery::getList(),
                'label' => 'Shift Category'
            ],
            [
                'attribute' => 'ssr_sst_id',
                'value' => static function (
                    ShiftScheduleRule $model
                ) {
                    return $model->scheduleType ? $model->scheduleType->getColorLabel() . '&nbsp; ' .
                        $model->getScheduleTypeTitle() : '-';
                },
                'filter' => ShiftScheduleType::getList(),
                'format' => 'raw'
            ],
            'ssr_title',
            'ssr_start_time_utc',
            'ssr_end_time_utc',
            'ssr_duration_time',

            [
                'label' => 'Start Loc Time',
                'value' => static function (
                    ShiftScheduleRule $model
                ) {
                    return '<i class="fa fa-clock-o"></i> ' . Yii::$app->formatter->asTime(strtotime($model->ssr_start_time_utc));
                },
                'format' => 'raw'
            ],
            [
                'label' => 'End Loc Time',
                'value' => static function (
                    ShiftScheduleRule $model
                ) {
                    return '<i class="fa fa-clock-o"></i> ' . Yii::$app->formatter->asTime(strtotime($model->ssr_end_time_utc));
                },
                'format' => 'raw'
            ],
            //'ssr_timezone',
//            'ssr_start_time_loc',
//            'ssr_end_time_loc',

            /*[
                'class' => DurationColumn::class,
                'attribute' => 'ssr_duration_time',
                'startAttribute' => 'ssr_duration_time',
                'options' => ['style' => 'width:180px'],
            ],*/
            'ssr_cron_expression',
            'ssr_cron_expression_exclude',
            ['class' => BooleanColumn::class, 'attribute' => 'ssr_enabled'],

//            [
//                'class' => DateTimeColumn::class,
//                'attribute' => 'ssr_created_dt',
//                'format' => 'byUserDateTime'
//            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ssr_updated_dt',
                'format' => 'byUserDateTime'
            ],
//            [
//                'class' => UserSelect2Column::class,
//                'attribute' => 'ssr_created_user_id',
//                'relation' => 'createdUser',
//                'format' => 'username',
//                'placeholder' => 'Select User'
//            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ssr_updated_user_id',
                'relation' => 'updatedUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
