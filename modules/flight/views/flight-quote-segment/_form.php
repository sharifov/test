<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteSegment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-quote-segment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fqs_flight_quote_id')->textInput() ?>

    <?= $form->field($model, 'fqs_flight_quote_trip_id')->textInput() ?>

    <?= $form->field($model, 'fqs_departure_dt')->textInput() ?>

    <?= $form->field($model, 'fqs_arrival_dt')->textInput() ?>

    <?= $form->field($model, 'fqs_stop')->textInput() ?>

    <?= $form->field($model, 'fqs_flight_number')->textInput() ?>

    <?= $form->field($model, 'fqs_booking_class')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fqs_duration')->textInput() ?>

    <?= $form->field($model, 'fqs_departure_airport_iata')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fqs_departure_airport_terminal')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fqs_arrival_airport_iata')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fqs_arrival_airport_terminal')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fqs_operating_airline')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fqs_marketing_airline')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fqs_air_equip_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fqs_marriage_group')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fqs_cabin_class')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fqs_meal')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fqs_fare_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fqs_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fqs_ticket_id')->textInput() ?>

    <?= $form->field($model, 'fqs_recheck_baggage')->textInput() ?>

    <?= $form->field($model, 'fqs_mileage')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
