<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\componentEvent\entity\search\ClientChatComponentEventSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-chat-component-event-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ccce_id') ?>

    <?= $form->field($model, 'ccce_chat_channel_id') ?>

    <?= $form->field($model, 'ccce_component') ?>

    <?= $form->field($model, 'ccce_event_type') ?>

    <?= $form->field($model, 'ccce_component_config') ?>

    <?php // echo $form->field($model, 'ccce_enabled') ?>

    <?php // echo $form->field($model, 'ccce_sort_order') ?>

    <?php // echo $form->field($model, 'ccce_created_user_id') ?>

    <?php // echo $form->field($model, 'ccce_updated_user_id') ?>

    <?php // echo $form->field($model, 'ccce_created_dt') ?>

    <?php // echo $form->field($model, 'ccce_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
