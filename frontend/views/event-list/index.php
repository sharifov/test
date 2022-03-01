<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\eventManager\src\entities\search\EventListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Event Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Event List', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'el_id',
            'el_key',
            'el_category',
            'el_description',
            'el_enable_type',
            //'el_enable_log',
            //'el_break',
            //'el_sort_order',
            //'el_cron_expression',
            //'el_condition:ntext',
            //'el_builder_json',
            //'el_updated_dt',
            //'el_updated_user_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, EventList $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'el_id' => $model->el_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
