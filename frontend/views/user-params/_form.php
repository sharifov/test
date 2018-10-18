<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserParams */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-params-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'up_user_id')->dropDownList(\common\models\Employee::getList()) ?>

    <?= $form->field($model, 'up_commission_percent')->input('number') ?>

    <?= $form->field($model, 'up_base_amount')->input('number') ?>

    <?= $form->field($model, 'up_bonus_active')->checkbox() ?>

    <?//= $form->field($model, 'up_updated_dt')->textInput() ?>

    <?//= $form->field($model, 'up_updated_user_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
