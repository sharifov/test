<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EmailTemplateType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-template-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'etp_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'etp_origin_name')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'etp_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'etp_hidden')->checkbox() ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
