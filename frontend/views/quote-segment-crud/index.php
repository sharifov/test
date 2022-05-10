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
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'qs_id',
            'qs_departure_time',
            'qs_arrival_time',
            'qs_flight_number',
            //'qs_booking_class',
            //'qs_duration',
            //'qs_departure_airport_code',
            //'qs_departure_airport_terminal',
            'qs_arrival_airport_code',
            //'qs_arrival_airport_terminal',
            'qs_operating_airline',
            //'qs_marketing_airline',
            //'qs_air_equip_type',
            //'qs_marriage_group',
            //'qs_mileage',
            //'qs_cabin',
            //'qs_cabin_basic',
            //'qs_meal',
            //'qs_fare_code',
            'qs_trip_id',
            //'qs_key',
            //'qs_created_dt',
            //'qs_updated_dt',
            //'qs_updated_user_id',
            //'qs_ticket_id',
            //'qs_recheck_baggage',
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
