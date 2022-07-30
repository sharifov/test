<?php

use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
use common\components\widgets\BaseForm;
use src\entities\email\form\EmailForm;

/* @var $this yii\web\View */
/* @var $emailForm src\entities\email\form\EmailForm */
?>
<div class="email-form">
    <?php $form = BaseForm::begin(); ?>

    <?= $form->field($emailForm, 'replyId')->textInput() ?>
    <?= $form->field($emailForm, 'leads')->textInput() ?>
    <?= $form->field($emailForm, 'projectId')->dropDownList($emailForm->listProjects()) ?>
    <?= $form->field($emailForm->contacts['from'], '[from]id')->simpleHidden(); ?>
    <?= $form->field($emailForm->contacts['to'], '[to]id')->simpleHidden(); ?>
    <?= $form->field($emailForm->contacts['from'], '[from]email')->textInput() ?>
    <?= $form->field($emailForm->contacts['from'], '[from]name')->textInput() ?>

    <?= $form->field($emailForm->contacts['to'], '[to]email')->textInput() ?>
    <?= $form->field($emailForm->contacts['to'], '[to]name')->textInput() ?>

    <?= $form->field($emailForm->body, 'subject')->textInput(['maxlength' => true]) ?>

    <?= $form->field($emailForm->body, 'bodyHtml')->widget(CKEditor::class, [
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

    <?= $form->field($emailForm, 'type')->dropDownList($emailForm->listTypes()) ?>

    <?= $form->field($emailForm->params, 'templateType')->textInput() ?>
    <?= $form->field($emailForm->params, 'language')->dropDownList($emailForm->params->listLanguages()) ?>

     <?= $form->field($emailForm->log, 'communicationId')->textInput() ?>
    <?= $form->field($emailForm, 'isDeleted')->checkbox() ?>

    <?= $form->field($emailForm->log, 'isNew')->checkbox() ?>

     <?= $form->field($emailForm->params, 'priority')->dropDownList($emailForm->params->listPriorities()) ?>

    <?= $form->field($emailForm, 'status')->dropDownList($emailForm->listStatuses()) ?>

    <?= $form->field($emailForm->log, 'statusDoneDt')->textInput() ?>
    <?= $form->field($emailForm->log, 'readDt')->textInput() ?>
    <?= $form->field($emailForm->log, 'errorMessage')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php BaseForm::end(); ?>
</div>