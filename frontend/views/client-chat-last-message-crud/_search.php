<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatLastMessage\entity\ClientChatLastMessageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-last-message-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cclm_id') ?>

    <?= $form->field($model, 'cclm_cch_id') ?>

    <?= $form->field($model, 'cclm_type_id') ?>

    <?= $form->field($model, 'cclm_message') ?>

    <?= $form->field($model, 'cclm_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
