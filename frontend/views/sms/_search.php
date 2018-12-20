<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\SmsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sms-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 's_id') ?>

    <?= $form->field($model, 's_reply_id') ?>

    <?= $form->field($model, 's_lead_id') ?>

    <?= $form->field($model, 's_project_id') ?>

    <?= $form->field($model, 's_phone_from') ?>

    <?php // echo $form->field($model, 's_phone_to') ?>

    <?php // echo $form->field($model, 's_sms_text') ?>

    <?php // echo $form->field($model, 's_sms_data') ?>

    <?php // echo $form->field($model, 's_type_id') ?>

    <?php // echo $form->field($model, 's_template_type_id') ?>

    <?php // echo $form->field($model, 's_language_id') ?>

    <?php // echo $form->field($model, 's_communication_id') ?>

    <?php // echo $form->field($model, 's_is_deleted') ?>

    <?php // echo $form->field($model, 's_is_new') ?>

    <?php // echo $form->field($model, 's_delay') ?>

    <?php // echo $form->field($model, 's_priority') ?>

    <?php // echo $form->field($model, 's_status_id') ?>

    <?php // echo $form->field($model, 's_status_done_dt') ?>

    <?php // echo $form->field($model, 's_read_dt') ?>

    <?php // echo $form->field($model, 's_error_message') ?>

    <?php // echo $form->field($model, 's_tw_price') ?>

    <?php // echo $form->field($model, 's_tw_sent_dt') ?>

    <?php // echo $form->field($model, 's_tw_account_sid') ?>

    <?php // echo $form->field($model, 's_tw_message_sid') ?>

    <?php // echo $form->field($model, 's_tw_num_segments') ?>

    <?php // echo $form->field($model, 's_tw_to_country') ?>

    <?php // echo $form->field($model, 's_tw_to_state') ?>

    <?php // echo $form->field($model, 's_tw_to_city') ?>

    <?php // echo $form->field($model, 's_tw_to_zip') ?>

    <?php // echo $form->field($model, 's_tw_from_country') ?>

    <?php // echo $form->field($model, 's_tw_from_state') ?>

    <?php // echo $form->field($model, 's_tw_from_city') ?>

    <?php // echo $form->field($model, 's_tw_from_zip') ?>

    <?php // echo $form->field($model, 's_created_user_id') ?>

    <?php // echo $form->field($model, 's_updated_user_id') ?>

    <?php // echo $form->field($model, 's_created_dt') ?>

    <?php // echo $form->field($model, 's_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
