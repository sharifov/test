<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteSegment */

$this->title = $model->fqs_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Segments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="flight-quote-segment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->fqs_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->fqs_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'fqs_id',
            'fqs_flight_quote_id',
            'fqs_flight_quote_trip_id',
            'fqs_departure_dt',
            'fqs_arrival_dt',
            'fqs_stop',
            'fqs_flight_number',
            'fqs_booking_class',
            'fqs_duration',
            'fqs_departure_airport_iata',
            'fqs_departure_airport_terminal',
            'fqs_arrival_airport_iata',
            'fqs_arrival_airport_terminal',
            'fqs_operating_airline',
            'fqs_marketing_airline',
            'fqs_air_equip_type',
            'fqs_marriage_group',
            'fqs_cabin_class',
            'fqs_meal',
            'fqs_fare_code',
            'fqs_key',
            'fqs_ticket_id',
            'fqs_recheck_baggage',
            'fqs_mileage',
        ],
    ]) ?>

</div>
