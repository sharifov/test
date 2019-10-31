<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Conference */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="conference-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cf_cr_id')->textInput() ?>

    <?= $form->field($model, 'cf_sid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cf_status_id')->textInput() ?>

    <?= $form->field($model, 'cf_options')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'cf_created_dt')->textInput() ?>

    <?= $form->field($model, 'cf_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
