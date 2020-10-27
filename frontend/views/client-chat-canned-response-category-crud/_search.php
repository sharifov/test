<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\cannedResponseCategory\entity\search\ClientChatCannedResponseCategorySearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-chat-canned-response-category-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'crc_id') ?>

    <?= $form->field($model, 'crc_name') ?>

    <?= $form->field($model, 'crc_enabled') ?>

    <?= $form->field($model, 'crc_created_dt') ?>

    <?= $form->field($model, 'crc_updated_dt') ?>

    <?php // echo $form->field($model, 'crc_created_user_id') ?>

    <?php // echo $form->field($model, 'crc_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
