<?php

use modules\shiftSchedule\src\entities\shiftScheduleRequestLog\ShiftScheduleRequestLog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\shiftSchedule\src\entities\shiftScheduleRequestLog\search\ShiftScheduleRequestLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shift Schedule Request Histories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-schedule-request-history-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Shift Schedule Request History', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ssrh_id',
            [
                'attribute' => 'ssrh_ssr_id',
                'value' => static function (ShiftScheduleRequestLog $model) {
                    return Html::a('<i class="fa fa-link"></i> ' . $model->ssrh_ssr_id, Url::to(['/user-shift-schedule-crud/view', 'id' => $model->ssrh_ssr_id]), [
                        'target' => '_blank',
                        'data-pjax' => 0
                    ]);
                },
                'format' => 'raw'
            ],
            'ssrh_ssr_id',
            'ssrh_old_attr',
            'ssrh_new_attr',
            'ssrh_formatted_attr',
            //'ssrh_created_dt',
            //'ssrh_updated_dt',
            //'ssrh_created_user_id',
            //'ssrh_updated_user_id',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, ShiftScheduleRequestLog $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'ssrh_id' => $model->ssrh_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
