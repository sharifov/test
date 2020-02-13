<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SmsTemplateType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sms-template-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'stp_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'stp_origin_name')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'stp_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'stp_dep_id')->dropDownList(\common\models\Department::getList(), ['prompt' => '-']) ?>

    <?= $form->field($model, 'stp_hidden')->checkbox() ?>

    <?php /*= $form->field($model, 'stp_created_user_id')->textInput() ?>

    <?= $form->field($model, 'stp_updated_user_id')->textInput() ?>

    <?= $form->field($model, 'stp_created_dt')->textInput() ?>

    <?= $form->field($model, 'stp_updated_dt')->textInput()*/ ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
