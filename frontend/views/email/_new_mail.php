<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Email */
/* @var $form yii\widgets\ActiveForm */
/* @var $mailList [] */
/* @var $action string */


?>
<div class="inbox-body">
    <div class="mail_heading row">
        <div class="col-md-8">
            <div class="btn-group">
                <?php /*<button class="btn btn-sm btn-primary" type="button"><i class="fa fa-send"></i> Send Message</button>
                <button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Forward"><i class="fa fa-share"></i></button>
                <button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Print"><i class="fa fa-print"></i></button>*/ ?>

                <?/*= Html::a('<i class="fa fa-trash-o"></i>', ['delete', 'id' => $model->e_id], [
                    'class' => 'btn btn-sm btn-default',
                    'data-placement' => 'top',
                    'data-toggle' => 'tooltip',
                    'data-original-title' => 'Trash',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this message?',
                        'method' => 'post',
                    ],
                ])*/ ?>


            </div>
        </div>
        <div class="col-md-4 text-right">
            <p class="date"><i class="fa fa-calendar"></i> <?=Yii::$app->formatter->asDatetime(time())?></p>
        </div>
        <div class="col-md-12">
            <?php if($action === 'create'):?>
                <h4>Create new mail</h4>
            <?php endif; ?>

            <?php if($action === 'update'):?>
                <h4>Edit mail message</h4>
            <?php endif; ?>

            <?php if($action === 'reply'):?>
                <h4>Reply message</h4>
            <?php endif; ?>

        </div>
    </div>

    <div class="view-mail">

        <?php $form = ActiveForm::begin(); ?>


        <?php echo $form->errorSummary($model)?>

        <?//= $form->field($model, 'e_reply_id')->textInput() ?>
        <?//= $form->field($model, 'e_lead_id')->textInput() ?>

        <?//= $form->field($model, 'e_project_id')->textInput() ?>

        <?= $form->field($model, 'e_id')->hiddenInput()->label(false); ?>
        <?//= $form->field($model, 'e_message_id')->hiddenInput()->label(false); ?>
        <?= $form->field($model, 'e_ref_message_id')->hiddenInput()->label(false); ?>


        <?= Html::hiddenInput('e_send', 0, ['id' => 'e_send']); ?>

        <div class="row">
            <div class="col-md-4">
                <?php if($action === 'create' || $action === 'update' || $action === 'reply'):?>
                    <?= $form->field($model, 'e_email_from')->textInput(['maxlength' => true, 'readonly' => true]) ?>
                <?php else: ?>
                    <?= $form->field($model, 'e_email_from')->dropDownList($mailList, ['prompt' => '--- select email ---']) ?>
                <?php endif; ?>

                <?//= $form->field($model, 'e_email_from')->textInput(['maxlength' => true]) ?>

            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'e_email_to')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="clearfix"></div>
        <?//= $form->field($model, 'e_email_cc')->textInput(['maxlength' => true]) ?>
        <?//= $form->field($model, 'e_email_bc')->textInput(['maxlength' => true]) ?>

        <div class="row">
            <div class="col-md-8">
                <?= $form->field($model, 'e_email_subject')->textInput(['maxlength' => true]) ?>
            </div>
            <?/*
            <div class="col-md-4">
                <?= $form->field($model, 'e_message_id')->textInput(['readonly' => true]) ?>
            </div>*/?>
        </div>
        <div class="clearfix"></div>



        <?//= $form->field($model, 'e_email_body_html')->textarea(['rows' => 6]) ?>

        <?php
           /* echo $form->field($model, 'e_email_body_html')->widget(\vova07\imperavi\Widget::class, [
                'settings' => [
                    'lang' => 'en',
                    'minHeight' => 400,
                    'plugins' => [
                        'clips',
                        'fullscreen',
                    ],
                    'clips' => [
                        ['Lorem ipsum...', 'Lorem...'],
                        ['red', '<span class="label-red">red</span>'],
                        ['green', '<span class="label-green">green</span>'],
                        ['blue', '<span class="label-blue">blue</span>'],
                    ],
                ],
            ]);*/

        ?>


        <?= $form->field($model, 'e_email_body_html')->widget(\dosamigos\ckeditor\CKEditor::class, [
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
                //'removePlugins' => 'elementspath',
            ]
        ]) ?>


        <?//= $form->field($model, 'e_email_body_text')->textarea(['rows' => 6]) ?>

        <?//= $form->field($model, 'e_attach')->textInput(['maxlength' => true]) ?>

        <?//= $form->field($model, 'e_email_data')->textarea(['rows' => 6]) ?>

        <?//= $form->field($model, 'e_type_id')->textInput() ?>

        <?//= $form->field($model, 'e_template_type_id')->textInput() ?>

        <?//= $form->field($model, 'e_language_id')->textInput(['maxlength' => true]) ?>

        <?//= $form->field($model, 'e_communication_id')->textInput() ?>

        <?//= $form->field($model, 'e_is_deleted')->textInput() ?>

        <?//= $form->field($model, 'e_is_new')->textInput() ?>

        <?//= $form->field($model, 'e_delay')->textInput() ?>

        <?//= $form->field($model, 'e_priority')->textInput() ?>

        <?//= $form->field($model, 'e_status_id')->textInput() ?>

        <?//= $form->field($model, 'e_status_done_dt')->textInput() ?>

        <?//= $form->field($model, 'e_read_dt')->textInput() ?>

        <?//= $form->field($model, 'e_error_message')->textInput(['maxlength' => true]) ?>


        <div class="form-group text-center">
            <?/*= Html::submitButton('<i class="fa fa-edit"></i> Save', ['class' => 'btn btn-warning'])*/ ?>
            <?= Html::submitButton('<i class="fa fa-send"></i> Send Message', ['class' => 'btn btn-success', 'onclick' => '$("#e_send").val(1)']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>