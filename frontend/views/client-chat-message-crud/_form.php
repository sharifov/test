<?php

use src\model\clientChat\ClientChatPlatform;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\clientChatMessage\entity\ClientChatMessage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-message-form">

    <div class="col-md-6">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ccm_rid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ccm_client_id')->textInput() ?>

    <?= $form->field($model, 'ccm_user_id')->textInput() ?>

    <?= $form->field($model, 'ccm_platform_id')->dropDownList(ClientChatPlatform::getListName(), [
        'prompt' => '---'
    ]) ?>

    <?php //= $form->field($model, 'ccm_body')->textInput()?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </div>

</div>
