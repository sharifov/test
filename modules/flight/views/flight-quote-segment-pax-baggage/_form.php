<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteSegmentPaxBaggage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-quote-segment-pax-baggage-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'qsb_flight_pax_code_id')->textInput() ?>

    <?= $form->field($model, 'qsb_flight_quote_segment_id')->textInput() ?>

    <?= $form->field($model, 'qsb_airline_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qsb_allow_pieces')->textInput() ?>

    <?= $form->field($model, 'qsb_allow_weight')->textInput() ?>

    <?= $form->field($model, 'qsb_allow_unit')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qsb_allow_max_weight')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qsb_allow_max_size')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
