<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteTicket */
/* @var $form ActiveForm */
?>

<div class="flight-quote-ticket-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'fqt_pax_id')->textInput() ?>

        <?= $form->field($model, 'fqt_flight_id')->textInput() ?>

        <?= $form->field($model, 'fqt_ticket_number')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
