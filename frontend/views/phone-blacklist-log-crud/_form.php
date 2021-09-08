<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PhoneBlacklistLog */
/* @var $form ActiveForm */
?>

<div class="phone-blacklist-log-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'pbll_phone')->textInput() ?>
        <?php /*= $form->field($model, 'pbll_phone')->widget(\borales\extensions\phoneInput\PhoneInput::class, [
            'jsOptions' => [
                'nationalMode' => false,
                'preferredCountries' => ['us'],
                'customContainer' => 'intl-tel-input'
            ],
            'options' => [
                'onkeydown' => '
                                    return !validationField.validate(event);
                                ',
                'onkeyup' => '
                                    var value = $(this).val();
                                    $(this).val(value.replace(/[^0-9\+]+/g, ""));
                                '
            ]
        ]) */ ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
