<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\clientChatUserChannel\entity\ClientChatUserChannel */
/* @var $form ActiveForm */
?>

<div class="client-chat-user-channel-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ccuc_user_id')->widget(\src\widgets\UserSelect2Widget::class, [
            'data' => $model->ccuc_user_id ? [
                $model->ccuc_user_id => $model->ccucUser->username
            ] : [],
        ]) ?>

        <?= $form->errorSummary($model) ?>

        <?= $form->field($model, 'ccuc_channel_id')->textInput() ?>

        <?php // $form->field($model, 'ccuc_created_dt')->textInput() ?>

        <?php // $form->field($model, 'ccuc_created_user_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
