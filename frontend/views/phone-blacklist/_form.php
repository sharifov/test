<?php

use borales\extensions\phoneInput\PhoneInput;
use frontend\widgets\DateTimePickerWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/*use src\widgets\DateTimePicker;*/

/* @var $this yii\web\View */
/* @var $model common\models\PhoneBlacklist */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="phone-blacklist-form w-25">

    <?php $form = ActiveForm::begin(); ?>

    <?php /*= $form->field($model, 'pbl_phone')->widget(PhoneInput::class, [
        'jsOptions' => [
            'nationalMode' => false,
            'preferredCountries' => ['us'],
            'customContainer' => 'intl-tel-input'
        ]
    ])->label(false); */?>

    <?= $form->field($model, 'pbl_phone')->textInput() ?>

    <?= $form->field($model, 'pbl_description')->textarea() ?>

    <?= $form->field($model, 'pbl_enabled')->checkbox([1 => 'Yes', 0 => 'No']) ?>

    <?= $form->field($model, 'pbl_expiration_date')->widget(DateTimePickerWidget::class, [
            'clientOptions' => [
                'format' => 'yyyy-mm-dd hh:ii:ss',
                'todayBtn' => true
            ]
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
