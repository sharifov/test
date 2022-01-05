<?php

use sales\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\voip\phoneDevice\log\PhoneDeviceLog */
/* @var $form ActiveForm */
?>

<div class="phone-device-log-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'usa_user_id')->widget(UserSelect2Widget::class) ?>

        <?= $form->field($model, 'pdl_device_id')->textInput() ?>

        <?= $form->field($model, 'pdl_level')->textInput() ?>

        <?= $form->field($model, 'pdl_message')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pdl_error')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
