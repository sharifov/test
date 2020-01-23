<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Transaction */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transaction-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tr_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tr_invoice_id')->textInput() ?>

    <?= $form->field($model, 'tr_payment_id')->textInput() ?>

    <?= $form->field($model, 'tr_type_id')->textInput() ?>

    <?= $form->field($model, 'tr_date')->textInput() ?>

    <?= $form->field($model, 'tr_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tr_currency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tr_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
