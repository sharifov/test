<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightSegment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-segment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fs_flight_id')->textInput() ?>

    <?= $form->field($model, 'fs_origin_iata')->textInput() ?>

    <?= $form->field($model, 'fs_destination_iata')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fs_departure_date')->textInput() ?>

    <?= $form->field($model, 'fs_flex_type_id')->textInput() ?>

    <?= $form->field($model, 'fs_flex_days')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
