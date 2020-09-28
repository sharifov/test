<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatFeedback\entity\clientChatFeedbackSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-feedback-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ccf_id') ?>

    <?= $form->field($model, 'ccf_client_chat_id') ?>

    <?= $form->field($model, 'ccf_user_id') ?>

    <?= $form->field($model, 'ccf_client_id') ?>

    <?= $form->field($model, 'ccf_rating') ?>

    <?php // echo $form->field($model, 'ccf_message') ?>

    <?php // echo $form->field($model, 'ccf_created_dt') ?>

    <?php // echo $form->field($model, 'ccf_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
