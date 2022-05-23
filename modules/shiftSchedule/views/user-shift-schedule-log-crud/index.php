<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\shiftSchedule\src\entities\userShiftScheduleLog\UserShiftScheduleLog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\shiftSchedule\src\entities\userShiftScheduleLog\search\UserShiftScheduleLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Shift Schedule Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-shift-schedule-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Shift Schedule Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ussl_id',
            [
                'attribute' => 'ussl_uss_id',
                'value' => static function (UserShiftScheduleLog $model) {
                    return Html::a('<i class="fa fa-link"></i> ' . $model->ussl_uss_id, Url::to(['user-shift-schedule-crud/view', 'id' => $model->ussl_uss_id]));
                }
            ],
//            'ussl_old_attr',
//            'ussl_new_attr',
//            'ussl_formatted_attr',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ussl_created_user_id',
                'relation' => 'userCreated',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ussl_created_dt',
                'format' => 'byUserDateTime'
            ],
            //'ussl_month_start',
            //'ussl_year_start',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, UserShiftScheduleLog $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'ussl_id' => $model->ussl_id, 'ussl_month_start' => $model->ussl_month_start, 'ussl_year_start' => $model->ussl_year_start]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
