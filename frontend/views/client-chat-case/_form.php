<?php

use sales\model\clientChatCase\entity\ClientChatCase;
use sales\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ClientChatCase */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-case-form">

    <div class="row">
        <div class="col-md-4">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'cccs_chat_id')->textInput() ?>

            <?= $form->field($model, 'cccs_case_id')->textInput() ?>

            <?= $form->field($model, 'cccs_created_dt')->widget(DateTimePicker::class) ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
