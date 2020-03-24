<?php

use borales\extensions\phoneInput\PhoneInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneList\entity\PhoneList */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="phone-list-form">
    <div class="row">
        <div class="col-md-4">

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'pl_phone_number', [
                    'options' => [
                        'class' => 'form-group required',
                    ],
                ]
            )->widget(PhoneInput::class, [
                'options' => [
                    'required' => true,
                ],
                'jsOptions' => [
                    'nationalMode' => false,
                    'preferredCountries' => ['us'],
                    'customContainer' => 'intl-tel-input'
                ]
            ]) ?>

            <?= $form->field($model, 'pl_title')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'pl_enabled')->dropDownList([1 => 'Yes', 0 => 'No'], ['prompt' => 'Select...']) ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>