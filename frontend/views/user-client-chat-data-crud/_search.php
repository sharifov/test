<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\userClientChatData\entity\UserClientChatDataSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="user-client-chat-data-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'uccd_id') ?>

    <?= $form->field($model, 'uccd_employee_id') ?>

    <?= $form->field($model, 'uccd_created_dt') ?>

    <?= $form->field($model, 'uccd_updated_dt') ?>

    <?= $form->field($model, 'uccd_created_user_id') ?>

    <?php // echo $form->field($model, 'uccd_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
