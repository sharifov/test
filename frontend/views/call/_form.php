<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Call */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'c_call_sid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_account_sid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_call_type_id')->textInput() ?>

    <?= $form->field($model, 'c_from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_to')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_sip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_call_status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_api_version')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_direction')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_forwarded_from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_caller_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_parent_call_sid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_call_duration')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_sip_response_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_recording_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_recording_sid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_recording_duration')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_timestamp')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_uri')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_sequence_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_lead_id')->textInput() ?>

    <?= $form->field($model, 'c_created_user_id')->textInput() ?>

    <?= $form->field($model, 'c_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
