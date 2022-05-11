<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteTrip */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-trip-form row">

    <div class="col-md-6">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'qt_duration')->textInput() ?>

        <?= $form->field($model, 'qt_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'qt_quote_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>


</div>
