<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GlobalLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="global-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'gl_app_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gl_app_user_id')->textInput() ?>

    <?= $form->field($model, 'gl_model')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gl_obj_id')->textInput() ?>

    <?= $form->field($model, 'gl_old_attr')->textInput() ?>

    <?= $form->field($model, 'gl_new_attr')->textInput() ?>

    <?= $form->field($model, 'gl_formatted_attr')->textInput() ?>

    <?= $form->field($model, 'gl_created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
