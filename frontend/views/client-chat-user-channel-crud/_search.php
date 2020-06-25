<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatUserChannel\entity\search\ClientChatUserChannelSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-chat-user-channel-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ccuc_user_id') ?>

    <?= $form->field($model, 'ccuc_channel_id') ?>

    <?= $form->field($model, 'ccuc_created_dt') ?>

    <?= $form->field($model, 'ccuc_created_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
