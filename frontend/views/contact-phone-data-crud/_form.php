<?php

use sales\model\contactPhoneData\service\ContactPhoneDataDictionary;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\contactPhoneData\entity\ContactPhoneData */
/* @var $form ActiveForm */
?>

<div class="contact-phone-data-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cpd_cpl_id')->textInput() ?>

        <?= $form->field($model, 'cpd_key')->dropDownList(ContactPhoneDataDictionary::KEY_LIST) ?>

        <?= $form->field($model, 'cpd_value')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
