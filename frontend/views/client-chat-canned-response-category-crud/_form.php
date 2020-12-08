<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\cannedResponseCategory\entity\ClientChatCannedResponseCategory */
/* @var $form ActiveForm */
?>

<div class="client-chat-canned-response-category-form">

    <div class="col-md-3">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'crc_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'crc_enabled')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
