<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\QuotePrice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-price-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'quote_id')->textInput() ?>

    <?= $form->field($model, 'passenger_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'selling')->textInput() ?>

    <?= $form->field($model, 'net')->textInput() ?>

    <?= $form->field($model, 'fare')->textInput() ?>

    <?= $form->field($model, 'taxes')->textInput() ?>

    <?= $form->field($model, 'mark_up')->textInput() ?>

    <?= $form->field($model, 'extra_mark_up')->textInput() ?>

    <?= $form->field($model, 'created')->textInput() ?>

    <?= $form->field($model, 'updated')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
