<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\PaymentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pay_id') ?>

    <?= $form->field($model, 'pay_type_id') ?>

    <?= $form->field($model, 'pay_method_id') ?>

    <?= $form->field($model, 'pay_status_id') ?>

    <?= $form->field($model, 'pay_date') ?>

    <?php // echo $form->field($model, 'pay_amount') ?>

    <?php // echo $form->field($model, 'pay_currency') ?>

    <?php // echo $form->field($model, 'pay_invoice_id') ?>

    <?php // echo $form->field($model, 'pay_order_id') ?>

    <?php // echo $form->field($model, 'pay_created_user_id') ?>

    <?php // echo $form->field($model, 'pay_updated_user_id') ?>

    <?php // echo $form->field($model, 'pay_created_dt') ?>

    <?php // echo $form->field($model, 'pay_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
