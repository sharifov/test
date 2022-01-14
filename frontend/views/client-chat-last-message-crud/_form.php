<?php

use src\model\clientChat\ClientChatPlatform;
use src\model\clientChatLastMessage\entity\ClientChatLastMessage;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use src\widgets\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model src\model\clientChatLastMessage\entity\ClientChatLastMessage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-last-message-form">

    <div class="col-md-4">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cclm_cch_id')->textInput() ?>

        <?= $form->field($model, 'cclm_type_id')->dropDownList(ClientChatLastMessage::TYPE_LIST) ?>

        <?= $form->field($model, 'cclm_message')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'cclm_platform_id')->dropDownList(ClientChatPlatform::getListName(), [
            'prompt' => '---'
        ]) ?>

      <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
