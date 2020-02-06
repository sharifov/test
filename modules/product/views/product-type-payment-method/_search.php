<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\ProductTypePaymentMethod\search\ProductTypePaymentMethodSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-type-payment-method-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ptpm_produt_type_id') ?>

    <?= $form->field($model, 'ptpm_payment_method_id') ?>

    <?= $form->field($model, 'ptpm_payment_fee_percent') ?>

    <?= $form->field($model, 'ptpm_payment_fee_amount') ?>

    <?= $form->field($model, 'ptpm_enabled') ?>

    <?php // echo $form->field($model, 'ptpm_default') ?>

    <?php // echo $form->field($model, 'ptpm_created_user_id') ?>

    <?php // echo $form->field($model, 'ptpm_updated_user_id') ?>

    <?php // echo $form->field($model, 'ptpm_created_dt') ?>

    <?php // echo $form->field($model, 'ptpm_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
