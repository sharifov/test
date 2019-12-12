<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ClientEmail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-email-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'client_id')->textInput() ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'comments')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'type')->dropDownList($model::getEmailTypeList()) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
