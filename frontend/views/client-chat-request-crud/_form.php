<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatRequest\entity\ClientChatRequest */
/* @var $form ActiveForm */
?>

<div class="client-chat-request-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ccr_event')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ccr_json_data')->textarea(['rows' => 6]) ?>

        <?php // $form->field($model, 'ccr_created_dt')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
