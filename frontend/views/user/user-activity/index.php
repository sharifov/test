<?php

use common\components\grid\UserSelect2Column;
use modules\user\userActivity\entity\UserActivity;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\user\userActivity\entity\search\UserActivitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $eventList array */

$this->title = 'User Activities';
$this->params['breadcrumbs'][] = $this->title;

//\src\model\user\entity\monitor\UserMonitor::

?>
<div class="user-activity-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create User Activity', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ua_user_id',
                'relation' => 'user',
                'options' => ['style' => 'width:140px']
            ],
            [
                'attribute' => 'ua_object_event',
                'value' => static function (UserActivity $model) {
                    return $model->getEventName();
                },
                'filter' => $eventList,
                //'options' => ['style' => 'width: 80px']
            ],
            'ua_object_id',
            'ua_start_dt', // :datetime
            'ua_end_dt',
//            'ua_type_id',
            [
                'attribute' => 'ua_type_id',
                'value' => static function (UserActivity $model) {
                    return $model->getTypeName();
                },
                'filter' => UserActivity::getTypeList(),
                //'options' => ['style' => 'width: 80px']
            ],
            'ua_shift_event_id',
            'ua_description',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, UserActivity $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'ua_start_dt' => $model->ua_start_dt,
                        'ua_user_id' => $model->ua_user_id,
                        'ua_object_event' => $model->ua_object_event, 'ua_object_id' => $model->ua_object_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
