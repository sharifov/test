<?php

use common\models\QuoteSegment;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\QuoteSegmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quote Segments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-segment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <!--    <p>-->
    <!--        --><?php //// Html::a('Create Quote Segment', ['create'], ['class' => 'btn btn-success']) ?>
    <!--    </p>-->

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'qs_id',
            [
                'attribute' => 'qs_trip_id',
                'format' => 'raw',
                'value' => function (QuoteSegment $model) {
                    return '<i class="fa fa-link"></i> ' . Html::a($model->qs_trip_id, ['/quote-trip-crud/index', 'QuoteTripSearch[qt_id]' => $model->qs_trip_id], ['title' => 'Show', 'target' => '_blank', 'data-pjax' => 0]);
                }
            ],
            [
                'header' => 'Stop(s)',
                'format' => 'raw',
                'value' => function (QuoteSegment $model) {
                    $stops = $model->getQuoteSegmentStops()->all();
                    return $stops ? count($stops) : 0;
                }
            ],
            'qs_departure_time',
            'qs_arrival_time',
            'qs_flight_number',
            'qs_departure_airport_code',
            'qs_arrival_airport_code',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, QuoteSegment $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'qs_id' => $model->qs_id]);
                },
                'template' => '{view}',
            ],
        ],
    ]); ?>


</div>
