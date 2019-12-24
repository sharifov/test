<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CurrencyHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="currency-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ch_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ch_base_rate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ch_app_rate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ch_app_percent')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ch_created_date')->textInput() ?>

    <?= $form->field($model, 'ch_main_created_dt')->textInput() ?>

    <?= $form->field($model, 'ch_main_updated_dt')->textInput() ?>

    <?= $form->field($model, 'ch_main_synch_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
