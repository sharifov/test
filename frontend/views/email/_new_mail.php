<?php

use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
use common\components\widgets\BaseForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $mailList [] */
/* @var $action string */
/* @var $emailForm src\entities\email\form\EmailCreateForm */


?>
<div class="inbox-body">
    <div class="mail_heading row">
        <div class="col-md-12 text-right">
            <p class="date"><i class="fa fa-calendar"></i> <?=Yii::$app->formatter->asDatetime(time())?></p>
        </div>
        <div class="col-md-12">
            <h4>
            <?php if ($action === 'create') :?>
                Create new mail
            <?php elseif ($action === 'update') :?>
                Edit mail message
            <?php elseif ($action === 'reply') :?>
                Reply message
            <?php endif; ?>
            </h4>
        </div>
    </div>

    <div class="view-mail">

        <?php $form = BaseForm::begin(); ?>

        <?= $form->field($emailForm, 'emailId')->simpleHidden(); ?>
        <?= $form->field($emailForm, 'projectId')->simpleHidden(); ?>
        <?= $form->field($emailForm, 'depId')->simpleHidden(); ?>
        <?= $form->field($emailForm, 'type')->simpleHidden(); ?>
        <?= $form->field($emailForm, 'status')->simpleHidden(); ?>

		<?= $form->field($emailForm->contacts['from'], '[from]id')->simpleHidden(); ?>
		<?= $form->field($emailForm->contacts['to'], '[to]id')->simpleHidden(); ?>

        <div class="row">
            <div class="col-md-4">
                <?php if ($action) :?>
                    <?= $form->field($emailForm->contacts['from'], '[from]email')->textInput(['maxlength' => true, 'readonly' => true]) ?>
                    <?= $form->field($emailForm->contacts['from'], '[from]email')->simpleHidden(); ?>
                <?php else : ?>
                    <?= $form->field($emailForm->contacts['from'], '[from]email')->dropDownList($mailList, ['prompt' => '--- select email ---']) ?>
                <?php endif; ?>
                    <?= $form->field($emailForm->contacts['from'], '[from]type')->simpleHidden(); ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($emailForm->contacts['to'], '[to]email')->textInput(['maxlength' => true]) ?>
                <?= $form->field($emailForm->contacts['to'], '[to]type')->simpleHidden(); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <?= $form->field($emailForm->body, 'subject')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="clearfix"></div>

        <?= $form->field($emailForm->body, 'bodyHtml')->widget(CKEditor::class, [
            'options' => [
                'rows' => 6,
                'readonly' => false
            ],
            'preset' => 'custom',
            'clientOptions' => [
                'height' => 500,
                'fullPage' => true,
                'allowedContent' => true,
                'resize_enabled' => false,
                'removeButtons' => 'Subscript,Superscript,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe',
            ]
        ]) ?>
        <div class="form-group text-center">
            <?= Html::submitButton('<i class="fa fa-send"></i> Send Message', ['class' => 'btn btn-success']) ?>
        </div>

        <?php BaseForm::end(); ?>
    </div>
</div>