<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightPax */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-pax-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fp_flight_id')->textInput() ?>

    <?= $form->field($model, 'fp_pax_id')->textInput() ?>

    <?= $form->field($model, 'fp_pax_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fp_first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fp_last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fp_middle_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fp_dob')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
