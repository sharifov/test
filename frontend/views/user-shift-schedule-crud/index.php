<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use src\model\shiftSchedule\entity\shift\Shift;
use src\model\shiftSchedule\entity\shiftScheduleRule\ShiftScheduleRule;
use src\model\shiftSchedule\entity\userShiftSchedule\UserShiftSchedule;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\shiftSchedule\entity\userShiftSchedule\search\SearchUserShiftSchedule */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Shift Schedules';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-shift-schedule-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create User Shift Schedule', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-user-shift-schedule', 'scrollTo' => 0]); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            'uss_id',
            [
                'attribute' => 'uss_id',
                'options' => ['style' => 'width:100px']
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'uss_user_id',
                'relation' => 'user',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],
            //'uss_shift_id',
            //'uss_sst_id',
            [
                'attribute' => 'uss_sst_id',
                'value' => static function (
                    UserShiftSchedule $model
                ) {
                    return $model->getScheduleTypeTitle();
                },
                'filter' => ShiftScheduleType::getList()
            ],
            //'uss_ssr_id',

//            'uss_description',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'uss_start_utc_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'attribute' => 'uss_duration',
                'options' => ['style' => 'width:100px']
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'uss_end_utc_dt',
                'format' => 'byUserDateTime'
            ],
//            'uss_duration',

            [
                'attribute' => 'uss_shift_id',
                'value' => static function (
                    UserShiftSchedule $model
                ) {
                    return $model->getShiftTitle();
                },
                'filter' => Shift::getList()
            ],

            [
                'attribute' => 'uss_ssr_id',
                'value' => static function (
                    UserShiftSchedule $model
                ) {
                    return $model->getRuleTitle();
                },
                'filter' => ShiftScheduleRule::getList()
            ],
            //'uss_status_id',
            [
                'attribute' => 'uss_status_id',
                'value' => static function (
                    UserShiftSchedule $model
                ) {
                    return $model->getStatusName();
                },
                'filter' => UserShiftSchedule::getStatusList()
            ],
            [
                'attribute' => 'uss_type_id',
                'value' => static function (
                    UserShiftSchedule $model
                ) {
                    return $model->getTypeName();
                },
                'filter' => UserShiftSchedule::getTypeList()
            ],
//            'uss_type_id',
            'uss_customized:boolean',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'uss_created_dt',
                'format' => 'byUserDateTime'
            ],
//            [
//                'class' => DateTimeColumn::class,
//                'attribute' => 'uss_updated_dt',
//                'format' => 'byUserDateTime'
//            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'uss_created_user_id',
                'relation' => 'createdUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],
//            [
//                'class' => UserSelect2Column::class,
//                'attribute' => 'uss_updated_user_id',
//                'relation' => 'updatedUser',
//                'format' => 'username',
//                'placeholder' => 'Select User'
//            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
