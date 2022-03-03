<?php

use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\eventManager\src\entities\EventHandler;
use modules\eventManager\src\entities\EventList;
use modules\eventManager\src\services\EventService;
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
        <?= Html::a('<i class="fa fa-plus"></i> Create Event Handler', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fa fa-list"></i> Event List', ['event-list/index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'eh_id',
            'eh_el_id',
            'eh_class',
            'eh_method',
            [
                'attribute' => 'eh_enable_type',
                'value' => static function (EventHandler $model) {
                    return EventService::getEnableTypeLabel($model->eh_enable_type);
                },
                'format' => 'raw',
                'filter' => EventList::getEnableTypeList()
            ],
            [
                'attribute' => 'eh_break',
                'class' => BooleanColumn::class,
            ],

            [
                'attribute' => 'eh_enable_log',
                'class' => BooleanColumn::class,
            ],

            [
                'attribute' => 'eh_asynch',
                'class' => BooleanColumn::class,
            ],

            'eh_sort_order',
            //'eh_cron_expression',
            //'eh_condition:ntext',
            //'eh_builder_json',

            [
                'attribute' => 'eh_cron_expression',
                'value' => static function (EventHandler $model) {
                    return $model->eh_cron_expression ?
                        Html::tag('pre', Html::encode($model->eh_cron_expression)) : '-';
                },
                'format' => 'raw',
            ],
            'eh_params',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'eh_updated_dt',
                'format' => 'byUserDateTime',
            ],

//            'el_updated_dt',
//            'el_updated_user_id',

            [
                'attribute' => 'eh_updated_user_id',
                'class' => UserSelect2Column::class,
                'relation' => 'elUpdatedUser',
            ],

            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, EventHandler $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'eh_id' => $model->eh_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
