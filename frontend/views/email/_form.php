<?php

use dosamigos\ckeditor\CKEditor;
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

    <?= $form->field($model, 'e_email_from_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'e_email_to')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'e_email_to_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'e_email_cc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'e_email_bc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'e_email_subject')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'body_html')->widget(CKEditor::class, [
        'options' => [
            'rows' => 6,
            'readonly' => false
        ],
        'preset' => 'custom',
        'clientOptions' => [
            'height' => 300,
            'fullPage' => true,
            'allowedContent' => true,
            'resize_enabled' => false,
            'removeButtons' => 'Subscript,Superscript,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe',
        ]
    ]) ?>

    <?= $form->field($model, 'e_attach')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'e_email_data')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'e_type_id')->textInput() ?>

    <?= $form->field($model, 'e_template_type_id')->textInput() ?>

    <?= $form->field($model, 'e_language_id')->dropDownList(\lajax\translatemanager\models\Language::getLanguageNames()) ?>

    <?= $form->field($model, 'e_communication_id')->textInput() ?>

    <?= $form->field($model, 'e_is_deleted')->checkbox() ?>

    <?= $form->field($model, 'e_is_new')->checkbox() ?>

    <?= $form->field($model, 'e_delay')->textInput() ?>

    <?= $form->field($model, 'e_priority')->dropDownList(\common\models\Email::PRIORITY_LIST) ?>

    <?= $form->field($model, 'e_status_id')->dropDownList(\common\models\Email::STATUS_LIST) ?>

    <?= $form->field($model, 'e_status_done_dt')->textInput() ?>

    <?= $form->field($model, 'e_read_dt')->textInput() ?>

    <?= $form->field($model, 'e_error_message')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
