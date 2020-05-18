<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\twilio\src\entities\voiceLog\VoiceLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="voice-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'vl_call_sid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_account_sid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_to')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_call_status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_api_version')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_direction')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_forwarded_from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_caller_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_parent_call_sid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_call_duration')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_sip_response_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_recording_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_recording_sid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_recording_duration')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_timestamp')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_callback_source')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_sequence_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
