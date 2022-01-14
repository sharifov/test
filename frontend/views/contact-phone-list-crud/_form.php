<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\contactPhoneList\entity\ContactPhoneList */
/* @var $form ActiveForm */
?>

<div class="contact-phone-list-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cpl_phone_number')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cpl_title')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
