<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProfitBonus */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="profit-bonus-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pb_user_id')->dropDownList(\common\models\Employee::getList()) ?>

    <?= $form->field($model, 'pb_min_profit')->input('number') ?>

    <?= $form->field($model, 'pb_bonus')->input('number') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
