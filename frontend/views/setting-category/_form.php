<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SettingCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="setting-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sc_name')->textInput(['maxlength' => true, 'style' => 'width: 320px']) ?>

    <?= $form->field($model, 'sc_enabled')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
