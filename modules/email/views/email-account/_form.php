<?php

use modules\email\src\entity\emailAccount\EmailAccount;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\email\src\entity\emailAccount\EmailAccount */
/* @var $form ActiveForm */
?>

<div class="email-account-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ea_email')->textInput(['maxlength' => true]) ?>

        <b>Imap settings example</b>

        <pre>
{
    "path": "imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX",
    "login": "login",
    "password": "password"
}        </pre>

        <?php
        try {
            echo $form->field($model, 'ea_imap_settings')->widget(
                \kdn\yii2\JsonEditor::class,
                [
                    'clientOptions' => [
                        'modes' => ['code', 'form', 'tree', 'view'], //'text',
                        'mode' => 'tree'
                    ],
                    //'collapseAll' => ['view'],
                    'expandAll' => ['tree', 'form'],
                ]
            );
        } catch (Exception $exception) {
            echo $form->field($model, 'ea_imap_settings')->textarea(['rows' => 5]);//->label($model->s_name);
        }
        ?>

        <?= $form->field($model, 'ea_gmail_command')->dropDownList(EmailAccount::GMAIL_COMMAND_LIST, ['prompt' => 'Select command']) ?>

        <?= $form->field($model, 'ea_gmail_token')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'ea_protocol')->dropDownList(EmailAccount::PROTOCOL_LIST, ['prompt' => 'Select protocol']) ?>

        <?php //= $form->field($model, 'ea_options')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'ea_active')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
