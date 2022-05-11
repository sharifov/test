<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSegment */

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
