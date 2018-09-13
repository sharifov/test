<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LeadFlightSegment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-flight-segment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'lead_id')->textInput() ?>

    <?= $form->field($model, 'origin')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'departure')->textInput() ?>

    <?= $form->field($model, 'created')->textInput() ?>

    <?= $form->field($model, 'updated')->textInput() ?>

    <?= $form->field($model, 'flexibility')->textInput() ?>

    <?= $form->field($model, 'flexibility_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'origin_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination_label')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
