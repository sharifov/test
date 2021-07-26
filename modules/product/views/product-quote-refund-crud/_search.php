<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteRefund\search\ProductQuoteRefundSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-quote-refund-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pqr_id') ?>

    <?= $form->field($model, 'pqr_order_refund_id') ?>

    <?= $form->field($model, 'pqr_selling_price') ?>

    <?= $form->field($model, 'pqr_penalty_amount') ?>

    <?= $form->field($model, 'pqr_processing_fee_amount') ?>

    <?php // echo $form->field($model, 'pqr_refund_amount') ?>

    <?php // echo $form->field($model, 'pqr_status_id') ?>

    <?php // echo $form->field($model, 'pqr_client_currency') ?>

    <?php // echo $form->field($model, 'pqr_client_currency_rate') ?>

    <?php // echo $form->field($model, 'pqr_client_selling_price') ?>

    <?php // echo $form->field($model, 'pqr_client_refund_amount') ?>

    <?php // echo $form->field($model, 'pqr_created_user_id') ?>

    <?php // echo $form->field($model, 'pqr_updated_user_id') ?>

    <?php // echo $form->field($model, 'pqr_created_dt') ?>

    <?php // echo $form->field($model, 'pqr_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
