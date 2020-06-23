<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\UserFailedLogin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-failed-login-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ufl_username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ufl_user_id')->textInput() ?>

    <?= $form->field($model, 'ufl_ua')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ufl_ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ufl_session_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ufl_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
