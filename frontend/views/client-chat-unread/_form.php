<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatUnread\entity\ClientChatUnread */
/* @var $form ActiveForm */
?>

<div class="client-chat-unread-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ccu_cc_id')->textInput(); ?>

        <?= $form->field($model, 'ccu_count')->textInput(); ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']); ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
