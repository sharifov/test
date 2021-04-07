<?php

use borales\extensions\phoneInput\PhoneInput;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderContact\OrderContact */
/* @var $form ActiveForm */
?>

<div class="order-contact-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'oc_order_id')->input('number') ?>

        <?= $form->field($model, 'oc_first_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'oc_last_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'oc_middle_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'oc_email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'oc_phone_number')->widget(PhoneInput::class, [
            'jsOptions' => [
                'formatOnDisplay' => false,
                'autoPlaceholder' => 'off',
                'customPlaceholder' => '',
                'allowDropdown' => false,
                //'preferredCountries' => ['us'],
                'customContainer' => 'intl-tel-input'
            ]
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
