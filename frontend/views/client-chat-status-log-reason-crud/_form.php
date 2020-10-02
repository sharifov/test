<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\statusLogReason\ClientChatStatusLogReason */
/* @var $form ActiveForm */
?>

<div class="client-chat-status-log-reason-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cslr_status_log_id')->textInput() ?>

        <?= $form->field($model, 'cslr_action_reason_id')->textInput() ?>

        <?= $form->field($model, 'cslr_comment')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
