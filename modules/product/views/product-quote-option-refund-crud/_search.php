<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteOptionRefund\search\ProductQuoteOptionRefund */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-quote-option-refund-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pqor_id') ?>

    <?= $form->field($model, 'pqor_product_quote_refund_id') ?>

    <?= $form->field($model, 'pqor_product_quote_option_id') ?>

    <?= $form->field($model, 'pqor_selling_price') ?>

    <?= $form->field($model, 'pqor_penalty_amount') ?>

    <?php // echo $form->field($model, 'pqor_processing_fee_amount') ?>

    <?php // echo $form->field($model, 'pqor_refund_amount') ?>

    <?php // echo $form->field($model, 'pqor_status_id') ?>

    <?php // echo $form->field($model, 'pqor_client_currency') ?>

    <?php // echo $form->field($model, 'pqor_client_currency_rate') ?>

    <?php // echo $form->field($model, 'pqor_client_selling_price') ?>

    <?php // echo $form->field($model, 'pqor_client_refund_amount') ?>

    <?php // echo $form->field($model, 'pqor_created_user_id') ?>

    <?php // echo $form->field($model, 'pqor_updated_user_id') ?>

    <?php // echo $form->field($model, 'pqor_created_dt') ?>

    <?php // echo $form->field($model, 'pqor_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
