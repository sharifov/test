<?php

use common\models\Employee;
use sales\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatNote\entity\ClientChatNote */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-note-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ccn_chat_id')->textInput() ?>

        <?= $form->field($model, 'ccn_user_id')->dropDownList(Employee::getList()) ?>

        <?= $form->field($model, 'ccn_note')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'ccn_deleted')->dropDownList([0 => 'No', 1 => 'Yes']) ?>

        <?= $form->field($model, 'ccn_created_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'ccn_updated_dt')->widget(DateTimePicker::class) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
