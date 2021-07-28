<?php

use common\models\Currency;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefundStatus;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefund */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-quote-object-refund-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-2">
      <?= $form->errorSummary($model) ?>

      <?= $form->field($model, 'pqor_product_quote_refund_id')->input('number') ?>

      <?= $form->field($model, 'pqor_selling_price')->input('number', ['step' => 'any']) ?>

      <?= $form->field($model, 'pqor_penalty_amount')->input('number', ['step' => 'any']) ?>

      <?= $form->field($model, 'pqor_processing_fee_amount')->input('number', ['step' => 'any']) ?>

      <?= $form->field($model, 'pqor_refund_amount')->input('number', ['step' => 'any']) ?>

      <?= $form->field($model, 'pqor_status_id')->dropDownList(ProductQuoteObjectRefundStatus::getList(), ['prompt' => '---']) ?>

      <div class="form-group">
          <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
      </div>
    </div>

    <div class="col-md-2">
        <?= $form->field($model, 'pqor_client_currency')->dropDownList(Currency::getList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'pqor_client_currency_rate')->input('number', ['step' => 'any']) ?>

        <?= $form->field($model, 'pqor_client_selling_price')->input('number', ['step' => 'any']) ?>

        <?= $form->field($model, 'pqor_client_refund_amount')->input('number', ['step' => 'any']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>