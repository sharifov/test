<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteBookingAirline */
/* @var $form ActiveForm */
?>

<div class="flight-quote-booking-airline-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'fqba_fqb_id')->textInput() ?>

        <?= $form->field($model, 'fqba_record_locator')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqba_airline_code')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
