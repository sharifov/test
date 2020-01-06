<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel modules\flight\models\search\FlightQuoteSegmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Quote Segments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-segment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Flight Quote Segment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'fqs_id',
            'fqs_flight_quote_id',
            'fqs_flight_quote_trip_id',
            'fqs_departure_dt',
            'fqs_arrival_dt',
            //'fqs_stop',
            //'fqs_flight_number',
            //'fqs_booking_class',
            //'fqs_duration',
            //'fqs_departure_airport_iata',
            //'fqs_departure_airport_terminal',
            //'fqs_arrival_airport_iata',
            //'fqs_arrival_airport_terminal',
            //'fqs_operating_airline',
            //'fqs_marketing_airline',
            //'fqs_air_equip_type',
            //'fqs_marriage_group',
            //'fqs_cabin_class',
            //'fqs_meal',
            //'fqs_fare_code',
            //'fqs_key',
            //'fqs_ticket_id',
            //'fqs_recheck_baggage',
            //'fqs_mileage',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
