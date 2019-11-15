<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Currency */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="currency-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cur_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cur_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cur_symbol')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cur_rate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cur_system_rate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cur_enabled')->textInput() ?>

    <?= $form->field($model, 'cur_default')->textInput() ?>

    <?= $form->field($model, 'cur_sort_order')->textInput() ?>

    <?//= $form->field($model, 'cur_created_dt')->textInput() ?>

    <?//= $form->field($model, 'cur_updated_dt')->textInput() ?>

    <?= $form->field($model, 'cur_synch_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
