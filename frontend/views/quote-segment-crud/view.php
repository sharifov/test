<?php

use common\components\grid\DateColumn;
use common\models\QuoteSegmentStop;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSegment */
/* @var $searchModel common\models\search\QuoteSegmentStopSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $model->qs_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Segments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quote-segment-view">

    <h1><?= Html::encode($this->title) ?></h1>

<!--    <p>-->
<!--        --><?php // Html::a('Update', ['update', 'qs_id' => $model->qs_id], ['class' => 'btn btn-primary']) ?>
<?php ////Html::a('Delete', ['delete', 'qs_id' => $model->qs_id], [
////            'class' => 'btn btn-danger',
////            'data' => [
////                'confirm' => 'Are you sure you want to delete this item?',
////                'method' => 'post',
////            ],
////        ]) ?>
<!--    </p>-->

    <div class="row">
        <div class="col-md-5">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'qs_id',
                    'qs_departure_time',
                    'qs_arrival_time',
                    'qs_stop',
                    'qs_flight_number',
                    'qs_booking_class',
                    'qs_duration',
                    'qs_departure_airport_code',
                    'qs_departure_airport_terminal',
                    'qs_arrival_airport_code',
                    'qs_arrival_airport_terminal',
                    'qs_operating_airline',
                    'qs_marketing_airline',
                    'qs_air_equip_type',
                    'qs_marriage_group',
                    'qs_mileage',
                    'qs_cabin',
                    'qs_cabin_basic',
                    'qs_meal',
                    'qs_fare_code',
                    'qs_trip_id',
                    'qs_key',
                    'qs_ticket_id',
                    'qs_recheck_baggage',
                ],
            ]) ?>
        </div>
        <div class="col-md-7">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'layout' => "{errors}\n{items}\n{pager}",
                'columns' => [
                    [
                        'attribute' => 'qss_id',
                        'format' => 'raw',
                        'value' => function (QuoteSegmentStop $model) {
                            return '<i class="fa fa-link"></i> ' .
                                Html::a(
                                    $model->qss_id,
                                    ['/quote-segment-stop-crud/index', 'QuoteSegmentStopSearch[qss_id]' => $model->qss_id],
                                    ['title' => 'Show', 'target' => '_blank', 'data-pjax' => 0]
                                );
                        }
                    ],
                    [
                        'label' => 'Location Code',
                        'attribute' => 'qss_location_code',
                        'value' => static function (QuoteSegmentStop $model) {
                            return Html::tag('span', $model->qss_location_code, ['class' => 'label label-info']);
                        },
                        'format' => 'raw',
                    ],
                    [
                        'class' => DateColumn::class,
                        'label' => 'Departure Dt',
                        'attribute' => 'qss_departure_dt',
                    ],
                    [
                        'class' => DateColumn::class,
                        'label' => 'Arrival Dt',
                        'attribute' => 'qss_arrival_dt',
                    ],
                    'qss_duration',
                    [
                        'class' => ActionColumn::class,
                        'urlCreator' => function ($action, QuoteSegmentStop $model, $key, $index, $column) {
                            return Url::to(['quote-segment-stop-crud/' . $action, 'qss_id' => $model->qss_id]);
                        },
                        'template' => '{view}',
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
