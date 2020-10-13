<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatChannelTransfer\entity\ClientChatChannelTransfer */
/* @var $form ActiveForm */
?>

<div class="client-chat-channel-transfer-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cctr_from_ccc_id')->dropDownList(\sales\model\clientChatChannel\entity\ClientChatChannel::getList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'cctr_to_ccc_id')->dropDownList(\sales\model\clientChatChannel\entity\ClientChatChannel::getList(), ['prompt' => '---']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
