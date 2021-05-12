<?php

use dosamigos\datetimepicker\DateTimePicker;
use sales\model\userClientChatData\entity\UserClientChatData;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;

/* @var yii\web\View $this */
/* @var sales\model\userClientChatData\entity\UserClientChatData $model */
/* @var ActiveForm $form */

?>

<div class="user-client-chat-data-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?php echo $form->field($model, 'uccd_employee_id')->textInput(['maxlength' => true]) ?>
        <?php echo $form->field($model, 'uccd_rc_user_id')->textInput(['maxlength' => true]) ?>
        <?php echo $form->field($model, 'uccd_auth_token')->textInput(['maxlength' => true]) ?>
        <?php echo $form->field($model, 'uccd_password')->passwordInput(['maxlength' => true]) ?>

        <?php echo $form->field($model, 'uccd_token_expired')->widget(DateTimePicker::class) ?>

        <?php echo $form->field($model, 'uccd_active')->dropDownList([1 => 'Yes', 0 => 'No']) ?>

        <?php echo $form->field($model, 'uccd_chat_status_id')->dropDownList(UserClientChatData::getChatStatusList()) ?>

        <div class="form-group">
            <?php echo Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
