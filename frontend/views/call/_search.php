<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\CallSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'c_id') ?>

    <?= $form->field($model, 'c_call_sid') ?>

    <?= $form->field($model, 'c_account_sid') ?>

    <?= $form->field($model, 'c_call_type_id') ?>

    <?= $form->field($model, 'c_from') ?>

    <?php // echo $form->field($model, 'c_to') ?>

    <?php // echo $form->field($model, 'c_sip') ?>

    <?php // echo $form->field($model, 'c_call_status') ?>

    <?php // echo $form->field($model, 'c_api_version') ?>

    <?php // echo $form->field($model, 'c_direction') ?>

    <?php // echo $form->field($model, 'c_forwarded_from') ?>

    <?php // echo $form->field($model, 'c_caller_name') ?>

    <?php // echo $form->field($model, 'c_parent_call_sid') ?>

    <?php // echo $form->field($model, 'c_call_duration') ?>

    <?php // echo $form->field($model, 'c_sip_response_code') ?>

    <?php // echo $form->field($model, 'c_recording_url') ?>

    <?php // echo $form->field($model, 'c_recording_sid') ?>

    <?php // echo $form->field($model, 'c_recording_duration') ?>

    <?php // echo $form->field($model, 'c_timestamp') ?>

    <?php // echo $form->field($model, 'c_uri') ?>

    <?php // echo $form->field($model, 'c_sequence_number') ?>

    <?php // echo $form->field($model, 'c_lead_id') ?>

    <?php // echo $form->field($model, 'c_created_user_id') ?>

    <?php // echo $form->field($model, 'c_created_dt') ?>

    <?php // echo $form->field($model, 'c_com_call_id') ?>

    <?php // echo $form->field($model, 'c_updated_dt') ?>

    <?php // echo $form->field($model, 'c_project_id') ?>

    <?php // echo $form->field($model, 'c_error_message') ?>

    <?php // echo $form->field($model, 'c_is_new') ?>

    <?php // echo $form->field($model, 'c_is_deleted') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
