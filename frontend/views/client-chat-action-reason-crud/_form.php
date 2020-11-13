<?php

use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\actionReason\ClientChatActionReason */
/* @var $form ActiveForm */
?>

<div class="client-chat-action-reason-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ccar_action_id')->dropDownList(ClientChatStatusLog::getActionList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'ccar_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ccar_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ccar_enabled')->checkbox() ?>

        <?= $form->field($model, 'ccar_comment_required')->checkbox() ?>

        <?php //= $form->field($model, 'ccar_created_user_id')->textInput() ?>

        <?php //= $form->field($model, 'ccar_updated_user_id')->textInput() ?>

        <?php //= $form->field($model, 'ccar_created_dt')->textInput() ?>

        <?php //= $form->field($model, 'ccar_updated_dt')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
