<?php

use borales\extensions\phoneInput\PhoneInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PhoneBlacklist */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="phone-blacklist-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pbl_phone')->widget(PhoneInput::class, [
        'jsOptions' => [
            'nationalMode' => false,
            'preferredCountries' => ['us'],
            'customContainer' => 'intl-tel-input'
        ]
    ])->label(false); ?>

    <?= $form->field($model, 'pbl_description')->textarea() ?>

    <?= $form->field($model, 'pbl_enabled')->checkbox([1 => 'Yes', 0 => 'No']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
