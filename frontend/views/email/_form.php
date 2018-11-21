<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Email */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'e_reply_id')->textInput() ?>

    <?= $form->field($model, 'e_lead_id')->textInput() ?>

    <?= $form->field($model, 'e_project_id')->textInput() ?>

    <?= $form->field($model, 'e_email_from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'e_email_to')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'e_email_cc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'e_email_bc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'e_email_subject')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'e_email_body_html')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'e_email_body_text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'e_attach')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'e_email_data')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'e_type_id')->textInput() ?>

    <?= $form->field($model, 'e_template_type_id')->textInput() ?>

    <?= $form->field($model, 'e_language_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'e_communication_id')->textInput() ?>

    <?= $form->field($model, 'e_is_deleted')->textInput() ?>

    <?= $form->field($model, 'e_is_new')->textInput() ?>

    <?= $form->field($model, 'e_delay')->textInput() ?>

    <?= $form->field($model, 'e_priority')->textInput() ?>

    <?= $form->field($model, 'e_status_id')->textInput() ?>

    <?= $form->field($model, 'e_status_done_dt')->textInput() ?>

    <?= $form->field($model, 'e_read_dt')->textInput() ?>

    <?= $form->field($model, 'e_error_message')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'e_created_user_id')->textInput() ?>

    <?= $form->field($model, 'e_updated_user_id')->textInput() ?>

    <?= $form->field($model, 'e_created_dt')->textInput() ?>

    <?= $form->field($model, 'e_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
