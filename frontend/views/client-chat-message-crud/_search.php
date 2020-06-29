<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatMessage\entity\search\ClientChatMessageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-message-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ccm_id') ?>

    <?= $form->field($model, 'ccm_rid') ?>

    <?= $form->field($model, 'ccm_client_id') ?>

    <?= $form->field($model, 'ccm_user_id') ?>

    <?= $form->field($model, 'ccm_sent_dt') ?>

    <?php // echo $form->field($model, 'ccm_body') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
