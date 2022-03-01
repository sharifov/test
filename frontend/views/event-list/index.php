<?php

use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\eventManager\src\entities\EventList;
use modules\eventManager\src\services\EventService;
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
        <?= Html::a('<i class="fa fa-plus"></i> Create Event List', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fa fa-list"></i> Event Handler List', ['event-handler/index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-close"></i> Clear Cache', ['clear-cache'], [
            'class' => 'btn btn-warning',
            'title' => 'Clear Event List cache',
            'data' => [
                'confirm' => 'Are you sure you want to clear Event List cache data?'
            ],
        ]) ?>
    </p>


    <p>
    <div class="col-md-4">
        <table class="table table-bordered">
            <tr>
                <th>
                    Enable Type
                </th>
                <th>
                    Description
                </th>
            </tr>
            <tr>
                <td>
                    <?php echo EventService::getEnableTypeLabel(EventList::ET_DISABLED)?>
                </td>
                <td>
                    <?php echo Html::encode(EventService::getEnableTypeDesc(EventList::ET_DISABLED))?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo EventService::getEnableTypeLabel(EventList::ET_ENABLED)?>
                </td>
                <td>
                    <?php echo Html::encode(EventService::getEnableTypeDesc(EventList::ET_ENABLED))?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo EventService::getEnableTypeLabel(EventList::ET_ENABLED_CONDITION)?>
                </td>
                <td>
                    <?php echo Html::encode(EventService::getEnableTypeDesc(EventList::ET_ENABLED_CONDITION))?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo EventService::getEnableTypeLabel(EventList::ET_DISABLED_CONDITION)?>
                </td>
                <td>
                    <?php echo Html::encode(EventService::getEnableTypeDesc(EventList::ET_DISABLED_CONDITION))?>
                </td>
            </tr>
        </table>
    </div>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'el_id',

//            [
//                'attribute' => 'el_key',
//                'value' => static function (EventList $model) {
//                    return '<b title="' . Html::encode($model->el_description) . '" data-toggle="tooltip">' .
//                        ($model->el_description ? '<i class="fa fa-info-circle info"></i> ' : '') .
//                        Html::encode($model->el_key) . '</b>';
//                },
//                'format' => 'raw',
//            ],
            [
                'attribute' => 'el_category',
                'value' => static function (EventList $model) {
                    return $model->el_category ? $model->el_category : '-';
                },
                'filter' => Yii::$app->event->categoryList
            ],
            [
                'attribute' => 'el_key',
                'value' => static function (EventList $model) {
                    return Html::tag(
                        'span',
                        Html::encode($model->el_key),
                        ['class' => 'label label-primary', 'style' => 'font-size: 12px']
                    );
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'el_enable_type',
                'value' => static function (EventList $model) {
                    return EventService::getEnableTypeLabel($model->el_enable_type);
                },
                'format' => 'raw',
                'filter' => EventList::getEnableTypeList()
            ],

            [
                'attribute' => 'el_enable_log',
                'class' => BooleanColumn::class,
            ],

            [
                'attribute' => 'el_break',
                'class' => BooleanColumn::class,
            ],

            'el_sort_order',
            [
                'attribute' => 'el_cron_expression',
                'value' => static function (EventList $model) {
                    return $model->el_cron_expression ?
                        Html::tag('pre', Html::encode($model->el_cron_expression)) : '-';
                },
                'format' => 'raw',
            ],
            //'el_condition:ntext',
            //'el_builder_json',

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'el_updated_dt',
                'format' => 'byUserDateTime',
            ],

//            'el_updated_dt',
//            'el_updated_user_id',

            [
                'attribute' => 'el_updated_user_id',
                'class' => UserSelect2Column::class,
                'relation' => 'elUpdatedUser',
            ],

            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, EventList $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'el_id' => $model->el_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
