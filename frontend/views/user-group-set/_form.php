<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserGroupSet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-group-set-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ugs_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ugs_enabled')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
