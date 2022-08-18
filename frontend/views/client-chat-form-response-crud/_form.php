<?php

use src\model\clientChatForm\entity\ClientChatForm;
use src\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\clientChatFormResponse\entity\ClientChatFormResponse */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-form-response-form">

    <div class="col-md-4">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ccfr_client_chat_id')->textInput() ?>

        <?= $form->field($model, 'ccfr_uid')->textInput() ?>

        <?= $form->field($model, 'ccfr_form_id')->dropDownList(ClientChatForm::getList(), ['prompt' => '---'])?>

        <?= $form->field($model, 'ccfr_value')->textInput() ?>

        <?= $form->field($model, 'ccfr_rc_created_dt')->widget(DateTimePicker::class) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
