<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\src\entities\flightQuoteTicketRefund\FlightQuoteTicketRefund */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-quote-ticket-refund-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fqtr_ticket_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fqtr_fqb_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
