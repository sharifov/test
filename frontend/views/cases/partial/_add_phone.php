<?php

use borales\extensions\phoneInput\PhoneInput;
use common\models\ClientPhone;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model src\forms\cases\CasesAddEmailForm */
/* @var $form yii\widgets\ActiveForm */
?>
<?php Pjax::begin(['id' => 'pjax-cases-add-phone-form', 'enablePushState' => false, 'enableReplaceState' => false]); ?>
<div class="cases-change-status">

    <?php $form = ActiveForm::begin([
        'action' => ['cases/add-phone', 'gid' => $model->caseGid],
        'method' => 'post',
        'options' => ['data-pjax' => true]
    ]); ?>

    <?php
    echo $form->errorSummary($model);
    ?>

    <?= $form->field($model, 'phone')->widget(PhoneInput::class, [
        'options' => [
            'class' => 'form-control lead-form-input-element',
            'onkeyup' => 'var value = $(this).val();$(this).val(value.replace(/[^0-9\+]+/g, ""));',
            'required' => true,
        ],
        'jsOptions' => [
            'nationalMode' => false,
            'preferredCountries' => ['us'],
            'customContainer' => 'intl-tel-input'
        ]
    ])->label(false); ?>

    <?= $form->field($model, 'type')->dropDownList(ClientPhone::getPhoneTypeList()) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Add Phone', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php Pjax::end(); ?>
