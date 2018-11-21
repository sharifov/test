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

    <?= $form->field($model, 'etp_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'etp_created_user_id')->textInput() ?>

    <?= $form->field($model, 'etp_updated_user_id')->textInput() ?>

    <?= $form->field($model, 'etp_created_dt')->textInput() ?>

    <?= $form->field($model, 'etp_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
