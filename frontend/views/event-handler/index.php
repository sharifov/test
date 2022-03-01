<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\eventManager\src\entities\search\EventHandlerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Event Handlers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-handler-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Event Handler', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'eh_id',
            'eh_el_id',
            'eh_class',
            'eh_method',
            'eh_enable_type',
            //'eh_enable_log',
            //'eh_asynch',
            //'eh_break',
            //'eh_sort_order',
            //'eh_cron_expression',
            //'eh_condition:ntext',
            //'eh_builder_json',
            //'eh_updated_dt',
            //'eh_updated_user_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, EventHandler $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'eh_id' => $model->eh_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
