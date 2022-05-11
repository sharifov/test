<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\QuoteSegmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-segment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'qs_id') ?>

    <?= $form->field($model, 'qs_departure_time') ?>

    <?= $form->field($model, 'qs_arrival_time') ?>

    <?= $form->field($model, 'qs_stop') ?>

    <?= $form->field($model, 'qs_flight_number') ?>

    <?php // echo $form->field($model, 'qs_booking_class') ?>

    <?php // echo $form->field($model, 'qs_duration') ?>

    <?php // echo $form->field($model, 'qs_departure_airport_code') ?>

    <?php // echo $form->field($model, 'qs_departure_airport_terminal') ?>

    <?php // echo $form->field($model, 'qs_arrival_airport_code') ?>

    <?php // echo $form->field($model, 'qs_arrival_airport_terminal') ?>

    <?php // echo $form->field($model, 'qs_operating_airline') ?>

    <?php // echo $form->field($model, 'qs_marketing_airline') ?>

    <?php // echo $form->field($model, 'qs_air_equip_type') ?>

    <?php // echo $form->field($model, 'qs_marriage_group') ?>

    <?php // echo $form->field($model, 'qs_mileage') ?>

    <?php // echo $form->field($model, 'qs_cabin') ?>

    <?php // echo $form->field($model, 'qs_cabin_basic') ?>

    <?php // echo $form->field($model, 'qs_meal') ?>

    <?php // echo $form->field($model, 'qs_fare_code') ?>

    <?php // echo $form->field($model, 'qs_trip_id') ?>

    <?php // echo $form->field($model, 'qs_key') ?>

    <?php // echo $form->field($model, 'qs_created_dt') ?>

    <?php // echo $form->field($model, 'qs_updated_dt') ?>

    <?php // echo $form->field($model, 'qs_updated_user_id') ?>

    <?php // echo $form->field($model, 'qs_ticket_id') ?>

    <?php // echo $form->field($model, 'qs_recheck_baggage') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
