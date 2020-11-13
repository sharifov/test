<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\channelTranslate\search\ClientChatChannelTranslateSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-channel-translate-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ct_channel_id') ?>

    <?= $form->field($model, 'ct_language_id') ?>

    <?= $form->field($model, 'ct_name') ?>

    <?= $form->field($model, 'ct_created_user_id') ?>

    <?= $form->field($model, 'ct_updated_user_id') ?>

    <?php // echo $form->field($model, 'ct_created_dt') ?>

    <?php // echo $form->field($model, 'ct_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
