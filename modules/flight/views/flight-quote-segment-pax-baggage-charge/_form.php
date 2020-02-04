<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteSegmentPaxBaggageCharge */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-quote-segment-pax-baggage-charge-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'qsbc_flight_pax_code_id')->textInput() ?>

    <?= $form->field($model, 'qsbc_flight_quote_segment_id')->textInput() ?>

    <?= $form->field($model, 'qsbc_first_piece')->textInput() ?>

    <?= $form->field($model, 'qsbc_last_piece')->textInput() ?>

    <?= $form->field($model, 'qsbc_origin_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qsbc_origin_currency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qsbc_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qsbc_client_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qsbc_client_currency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qsbc_max_weight')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qsbc_max_size')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
