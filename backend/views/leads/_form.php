<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Lead */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'client_id')->textInput() ?>

    <?= $form->field($model, 'employee_id')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'project_id')->textInput() ?>

    <?= $form->field($model, 'source_id')->textInput() ?>

    <?= $form->field($model, 'trip_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cabin')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'adults')->textInput() ?>

    <?= $form->field($model, 'children')->textInput() ?>

    <?= $form->field($model, 'infants')->textInput() ?>

    <?= $form->field($model, 'notes_for_experts')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created')->textInput() ?>

    <?= $form->field($model, 'updated')->textInput() ?>

    <?= $form->field($model, 'request_ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'request_ip_detail')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'offset_gmt')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'snooze_for')->textInput() ?>

    <?= $form->field($model, 'rating')->textInput() ?>

    <?= $form->field($model, 'called_expert')->textInput() ?>

    <?= $form->field($model, 'discount_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bo_flight_id')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
