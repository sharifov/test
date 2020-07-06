<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatUserAccess\entity\ClientChatUserAccess */
/* @var $form ActiveForm */
?>

<div class="client-chat-user-access-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ccua_cch_id')->textInput() ?>

        <?= $form->field($model, 'ccua_user_id')->widget(\sales\widgets\UserSelect2Widget::class, [
            'data' => $model->ccua_user_id ? [
                $model->ccua_user_id => $model->ccuaUser->username
            ] : [],
        ]) ?>

        <?= $form->field($model, 'ccua_status_id')->dropDownList($model::STATUS_LIST, ['prompt' => '---']) ?>

        <?= $form->field($model, 'ccua_created_dt')->widget(\sales\widgets\DateTimePicker::class) ?>

        <?= $form->field($model, 'ccua_updated_dt')->widget(\sales\widgets\DateTimePicker::class) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
