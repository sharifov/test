<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatUnread\entity\search\ClientChatUnreadSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-chat-unread-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
        ],
    ]); ?>

    <?= $form->field($model, 'ccu_cc_id'); ?>

    <?= $form->field($model, 'ccu_count'); ?>

    <?= $form->field($model, 'ccu_created_dt'); ?>

    <?= $form->field($model, 'ccu_updated_dt'); ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']); ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
