<?php

use sales\services\clientChatCouchNote\ClientChatCouchNoteForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var yii\widgets\ActiveForm $formType */
/** @var ClientChatCouchNoteForm $couchNoteForm */
?>

<h6 style="margin-top: 20px;">Supervisor message</h6>

<div class="row">

    <?php
        $form = ActiveForm::begin([
            'id' => $couchNoteForm->formName(),
            'class' => '',
            'enableClientValidation' => false,
            'enableAjaxValidation' => false,
            'action' => ['client-chat/ajax-couch-note'],
            'method' => 'post',
        ]);
    ?>

        <?php echo $form->field($couchNoteForm, 'rid')->hiddenInput()->label(false) ?>
        <?php echo $form->field($couchNoteForm, 'alias')->hiddenInput()->label(false) ?>

        <div class="col-sm-11">
            <?php echo $form->field($couchNoteForm, 'message')->textInput([
                'id' => 'couchNoteMessage',
                'maxlength' => true
            ])->label(false) ?>
        </div>
        <div class="col-sm-1">
            <?= Html::submitButton('Send', ['class' => 'btn btn-success js-couch-note-btn']) ?>
        </div>

    <?php $form::end() ?>
</div>
