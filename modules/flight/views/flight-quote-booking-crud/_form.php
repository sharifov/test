<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteBooking */
/* @var $form ActiveForm */
?>

<div class="flight-quote-booking-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'fqb_fqf_id')->textInput() ?>

        <?= $form->field($model, 'fqb_booking_id')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqb_pnr')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqb_gds')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqb_gds_pcc')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqb_validating_carrier')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
