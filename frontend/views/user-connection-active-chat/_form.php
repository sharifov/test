<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\userConnectionActiveChat\UserConnectionActiveChat */
/* @var $form ActiveForm */
?>

<div class="user-connection-active-chat-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ucac_conn_id')->textInput() ?>

        <?= $form->field($model, 'ucac_chat_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
