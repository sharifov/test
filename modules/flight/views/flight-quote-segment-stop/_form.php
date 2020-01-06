<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteSegmentStop */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-quote-segment-stop-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'qss_quote_segment_id')->textInput() ?>

    <?= $form->field($model, 'qss_location_iata')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qss_equipment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qss_elapsed_time')->textInput() ?>

    <?= $form->field($model, 'qss_duration')->textInput() ?>

    <?= $form->field($model, 'qss_departure_dt')->textInput() ?>

    <?= $form->field($model, 'qss_arrival_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
