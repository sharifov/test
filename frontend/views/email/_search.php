<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\EmailSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'e_id') ?>

    <?= $form->field($model, 'e_reply_id') ?>

    <?= $form->field($model, 'e_lead_id') ?>

    <?= $form->field($model, 'e_project_id') ?>

    <?= $form->field($model, 'e_email_from') ?>

    <?php // echo $form->field($model, 'e_email_to') ?>

    <?php // echo $form->field($model, 'e_email_cc') ?>

    <?php // echo $form->field($model, 'e_email_bc') ?>

    <?php // echo $form->field($model, 'e_email_subject') ?>

    <?php // echo $form->field($model, 'e_email_body_html') ?>

    <?php // echo $form->field($model, 'e_email_body_text') ?>

    <?php // echo $form->field($model, 'e_attach') ?>

    <?php // echo $form->field($model, 'e_email_data') ?>

    <?php // echo $form->field($model, 'e_type_id') ?>

    <?php // echo $form->field($model, 'e_template_type_id') ?>

    <?php // echo $form->field($model, 'e_language_id') ?>

    <?php // echo $form->field($model, 'e_communication_id') ?>

    <?php // echo $form->field($model, 'e_is_deleted') ?>

    <?php // echo $form->field($model, 'e_is_new') ?>

    <?php // echo $form->field($model, 'e_delay') ?>

    <?php // echo $form->field($model, 'e_priority') ?>

    <?php // echo $form->field($model, 'e_status_id') ?>

    <?php // echo $form->field($model, 'e_status_done_dt') ?>

    <?php // echo $form->field($model, 'e_read_dt') ?>

    <?php // echo $form->field($model, 'e_error_message') ?>

    <?php // echo $form->field($model, 'e_created_user_id') ?>

    <?php // echo $form->field($model, 'e_updated_user_id') ?>

    <?php // echo $form->field($model, 'e_created_dt') ?>

    <?php // echo $form->field($model, 'e_updated_dt') ?>

    <?php // echo $form->field($model, 'e_message_id') ?>

    <?php // echo $form->field($model, 'e_ref_message_id') ?>

    <?php // echo $form->field($model, 'e_inbox_created_dt') ?>

    <?php // echo $form->field($model, 'e_inbox_email_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
