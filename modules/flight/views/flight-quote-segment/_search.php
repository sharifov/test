<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\search\FlightQuoteSegmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-quote-segment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'fqs_id') ?>

    <?= $form->field($model, 'fqs_flight_quote_id') ?>

    <?= $form->field($model, 'fqs_flight_quote_trip_id') ?>

    <?= $form->field($model, 'fqs_departure_dt') ?>

    <?= $form->field($model, 'fqs_arrival_dt') ?>

    <?php // echo $form->field($model, 'fqs_stop') ?>

    <?php // echo $form->field($model, 'fqs_flight_number') ?>

    <?php // echo $form->field($model, 'fqs_booking_class') ?>

    <?php // echo $form->field($model, 'fqs_duration') ?>

    <?php // echo $form->field($model, 'fqs_departure_airport_iata') ?>

    <?php // echo $form->field($model, 'fqs_departure_airport_terminal') ?>

    <?php // echo $form->field($model, 'fqs_arrival_airport_iata') ?>

    <?php // echo $form->field($model, 'fqs_arrival_airport_terminal') ?>

    <?php // echo $form->field($model, 'fqs_operating_airline') ?>

    <?php // echo $form->field($model, 'fqs_marketing_airline') ?>

    <?php // echo $form->field($model, 'fqs_air_equip_type') ?>

    <?php // echo $form->field($model, 'fqs_marriage_group') ?>

    <?php // echo $form->field($model, 'fqs_cabin_class') ?>

    <?php // echo $form->field($model, 'fqs_meal') ?>

    <?php // echo $form->field($model, 'fqs_fare_code') ?>

    <?php // echo $form->field($model, 'fqs_key') ?>

    <?php // echo $form->field($model, 'fqs_ticket_id') ?>

    <?php // echo $form->field($model, 'fqs_recheck_baggage') ?>

    <?php // echo $form->field($model, 'fqs_mileage') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
