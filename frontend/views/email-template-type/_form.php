<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EmailTemplateType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-template-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
    <?= $form->field($model, 'etp_key')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'etp_origin_name')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'etp_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'etp_dep_id')->dropDownList(\common\models\Department::getList(), ['prompt' => '-']) ?>

    <?= $form->field($model, 'etp_hidden')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
