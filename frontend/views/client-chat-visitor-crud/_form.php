<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatVisitor\entity\ClientChatVisitor */
/* @var $form ActiveForm */
?>

<div class="client-chat-visitor-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ccv_cch_id')->textInput() ?>

        <?= $form->field($model, 'ccv_cvd_id')->textInput() ?>

        <?= $form->field($model, 'ccv_client_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
