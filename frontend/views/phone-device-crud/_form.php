<?php

use sales\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\voip\phoneDevice\device\PhoneDevice */
/* @var $form ActiveForm */
?>

<div class="phone-device-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'pd_hash')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pd_user_id')->widget(UserSelect2Widget::class, [
            'data' => $model->pd_user_id ? [
                $model->pd_user_id => $model->user->username
            ] : [],
        ]) ?>

        <?= $form->field($model, 'pd_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pd_device_identity')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pd_status_device')->dropDownList([1 => 'Yes', 0 => 'No'], ['prompt' => 'Select value']) ?>

        <?= $form->field($model, 'pd_status_speaker')->dropDownList([1 => 'Yes', 0 => 'No'], ['prompt' => 'Select value']) ?>

        <?= $form->field($model, 'pd_status_microphone')->dropDownList([1 => 'Yes', 0 => 'No'], ['prompt' => 'Select value']) ?>

        <?= $form->field($model, 'pd_ip_address')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
