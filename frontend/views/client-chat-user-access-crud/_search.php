<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatUserAccess\entity\search\ClientChatUserAccessSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-chat-user-access-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ccua_cch_id') ?>

    <?= $form->field($model, 'ccua_user_id') ?>

    <?= $form->field($model, 'ccua_status_id') ?>

    <?= $form->field($model, 'ccua_created_dt') ?>

    <?= $form->field($model, 'ccua_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
