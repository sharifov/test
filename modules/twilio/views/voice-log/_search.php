<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\twilio\src\entities\voiceLog\search\VoiceLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="voice-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'vl_id') ?>

    <?= $form->field($model, 'vl_call_sid') ?>

    <?= $form->field($model, 'vl_account_sid') ?>

    <?= $form->field($model, 'vl_from') ?>

    <?= $form->field($model, 'vl_to') ?>

    <?php // echo $form->field($model, 'vl_call_status') ?>

    <?php // echo $form->field($model, 'vl_api_version') ?>

    <?php // echo $form->field($model, 'vl_direction') ?>

    <?php // echo $form->field($model, 'vl_forwarded_from') ?>

    <?php // echo $form->field($model, 'vl_caller_name') ?>

    <?php // echo $form->field($model, 'vl_parent_call_sid') ?>

    <?php // echo $form->field($model, 'vl_call_duration') ?>

    <?php // echo $form->field($model, 'vl_sip_response_code') ?>

    <?php // echo $form->field($model, 'vl_recording_url') ?>

    <?php // echo $form->field($model, 'vl_recording_sid') ?>

    <?php // echo $form->field($model, 'vl_recording_duration') ?>

    <?php // echo $form->field($model, 'vl_timestamp') ?>

    <?php // echo $form->field($model, 'vl_callback_source') ?>

    <?php // echo $form->field($model, 'vl_sequence_number') ?>

    <?php // echo $form->field($model, 'vl_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
