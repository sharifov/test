<?php

use sales\model\clientChatLead\entity\ClientChatLead;
use sales\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ClientChatLead */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-lead-form">

    <div class="row">
        <div class="col-md-4">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'ccl_chat_id')->textInput() ?>

            <?= $form->field($model, 'ccl_lead_id')->textInput() ?>

            <?= $form->field($model, 'ccl_created_dt')->widget(DateTimePicker::class) ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
