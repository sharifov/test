<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatDataRequest\entity\search\ClientChatDataRequestSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-data-request-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ccdr_id') ?>

    <?= $form->field($model, 'ccdr_chat_id') ?>

    <?= $form->field($model, 'ccdr_data_json') ?>

    <?= $form->field($model, 'ccdr_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
