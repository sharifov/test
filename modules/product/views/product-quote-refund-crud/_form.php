<?php

use common\models\Currency;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteRefund\ProductQuoteRefund */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-quote-refund-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-2">
      <?= $form->errorSummary($model) ?>

      <?= $form->field($model, 'pqr_order_refund_id')->input('number') ?>
        
      <?= $form->field($model, 'pqr_product_quote_id')->input('number') ?>

      <?= $form->field($model, 'pqr_selling_price')->input('number', [
          'step' => 'any'
      ]) ?>
      <?= $form->field($model, 'pqr_penalty_amount')->input('number', [
          'step' => 'any'
      ]) ?>
      <?= $form->field($model, 'pqr_processing_fee_amount')->input('number', [
          'step' => 'any'
      ]) ?>
      <?= $form->field($model, 'pqr_refund_amount')->input('number', [
          'step' => 'any'
      ]) ?>

      <?= $form->field($model, 'pqr_status_id')->dropDownList(ProductQuoteRefundStatus::getList(), [
          'prompt' => '---'
      ]) ?>

      <div class="form-group">
          <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
      </div>
    </div>

    <div class="col-md-2">

        <?= $form->field($model, 'pqr_client_currency')->dropDownList(Currency::getList(), [
            'prompt' => '---'
        ]) ?>

        <?= $form->field($model, 'pqr_client_currency_rate')->input('number', [
            'step' => 'any'
        ]) ?>

        <?= $form->field($model, 'pqr_client_selling_price')->input('number', [
            'step' => 'any'
        ]) ?>

        <?= $form->field($model, 'pqr_client_refund_amount')->input('number', [
            'step' => 'any'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>
</div>
