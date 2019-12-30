<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\TransactionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transaction-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'tr_id') ?>

    <?= $form->field($model, 'tr_code') ?>

    <?= $form->field($model, 'tr_invoice_id') ?>

    <?= $form->field($model, 'tr_payment_id') ?>

    <?= $form->field($model, 'tr_type_id') ?>

    <?php // echo $form->field($model, 'tr_date') ?>

    <?php // echo $form->field($model, 'tr_amount') ?>

    <?php // echo $form->field($model, 'tr_currency') ?>

    <?php // echo $form->field($model, 'tr_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
