<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatNote\entity\ClientChatNoteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-note-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ccn_id') ?>

    <?= $form->field($model, 'ccn_chat_id') ?>

    <?= $form->field($model, 'ccn_user_id') ?>

    <?= $form->field($model, 'ccn_note') ?>

    <?= $form->field($model, 'ccn_deleted') ?>

    <?php // echo $form->field($model, 'ccn_created_dt') ?>

    <?php // echo $form->field($model, 'ccn_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
